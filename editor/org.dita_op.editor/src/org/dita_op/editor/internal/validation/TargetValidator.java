/**
 * Copyright (C) 2008 Claude Vedovini <http://vedovini.net/>.
 * 
 * This file is part of the DITA Open Platform <http://www.dita-op.org/>.
 * 
 * The DITA Open Platform is free software: you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or (at your
 * option) any later version.
 * 
 * The DITA Open Platform is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more
 * details.
 * 
 * You should have received a copy of the GNU General Public License along with
 * The DITA Open Platform. If not, see <http://www.gnu.org/licenses/>.
 */
package org.dita_op.editor.internal.validation;

import java.net.URI;
import java.net.URISyntaxException;
import java.util.List;

import org.dita_op.editor.internal.Activator;
import org.eclipse.core.resources.IFile;
import org.eclipse.core.resources.IMarker;
import org.eclipse.core.resources.IResource;
import org.eclipse.core.runtime.CoreException;
import org.eclipse.core.runtime.Path;
import org.eclipse.wst.sse.core.StructuredModelManager;
import org.eclipse.wst.sse.core.internal.provisional.IStructuredModel;
import org.eclipse.wst.sse.core.internal.provisional.text.IStructuredDocument;
import org.eclipse.wst.sse.core.internal.provisional.text.IStructuredDocumentRegion;
import org.eclipse.wst.sse.core.internal.provisional.text.ITextRegion;
import org.eclipse.wst.sse.core.internal.provisional.text.ITextRegionList;
import org.eclipse.wst.sse.ui.internal.reconcile.AbstractStructuredTextReconcilingStrategy;
import org.eclipse.wst.validation.internal.core.ValidationException;
import org.eclipse.wst.validation.internal.provisional.core.IReporter;
import org.eclipse.wst.validation.internal.provisional.core.IValidationContext;
import org.eclipse.wst.validation.internal.provisional.core.IValidator;
import org.eclipse.wst.xml.core.internal.regions.DOMRegionContext;
import org.eclipse.wst.xml.core.internal.validation.core.Helper;

@SuppressWarnings("restriction")//$NON-NLS-1$
public class TargetValidator implements IValidator {

	private static final String MISSING_REF_TARGET_MARKER = Activator.PLUGIN_ID
			+ ".missingTarget"; //$NON-NLS-1$

	private IStructuredDocument document;

	public void cleanup(IReporter reporter) {
	}

	public void validate(IValidationContext helper, IReporter reporter)
			throws ValidationException {
		String[] uris = helper.getURIs();

		if (uris.length > 0) {
			for (String uri : uris) {
				IFile file = (IFile) helper.loadModel(Helper.GET_FILE,
						new Object[] { uri });
				validate(file, reporter);
			}
		} else {
			List<IFile> files = (List<IFile>) helper.loadModel(
					Helper.GET_PROJECT_FILES,
					new Object[] { getClass().getName() });

			for (IFile file : files) {
				validate(file, reporter);
			}
		}
	}

	public void validate(IFile file, IReporter reporter)
			throws ValidationException {
		IStructuredModel model = null;

		try {
			model = StructuredModelManager.getModelManager().getExistingModelForRead(
					file);

			if (model != null) {
				document = model.getStructuredDocument();
				reporter.removeAllMessages(this, file);

				try {
					file.deleteMarkers(MISSING_REF_TARGET_MARKER, false,
							IResource.DEPTH_ZERO);
				} catch (CoreException e) {
					Activator.getDefault().log(e.getStatus());
				}

				IStructuredDocumentRegion[] regions = document.getStructuredDocumentRegions();
				for (int i = 0; i < regions.length && !reporter.isCancelled(); i++) {
					validate(regions[i], reporter, file);
				}
			}
		} finally {
			if (model != null) {
				model.releaseFromRead();
			}
		}
	}

	private void validate(IStructuredDocumentRegion structuredDocumentRegion,
			IReporter reporter, IFile file) {

		if (structuredDocumentRegion == null) {
			return;
		}

		if (isStartTag(structuredDocumentRegion)) {
			checkMissingTarget(structuredDocumentRegion, reporter, file);
		}
	}

	/**
	 * Determines whether the IStructuredDocumentRegion is a XML "start tag"
	 * since they need to be checked for proper XML attribute region sequences
	 * 
	 * @param structuredDocumentRegion
	 * 
	 */
	private boolean isStartTag(
			IStructuredDocumentRegion structuredDocumentRegion) {
		if ((structuredDocumentRegion == null)
				|| structuredDocumentRegion.isDeleted()) {
			return false;
		}
		return structuredDocumentRegion.getFirstRegion().getType() == DOMRegionContext.XML_TAG_OPEN;
	}

	private void checkMissingTarget(
			IStructuredDocumentRegion structuredDocumentRegion,
			IReporter reporter, IFile file) {

		if (structuredDocumentRegion.isDeleted()) {
			return;
		}

		ITextRegionList textRegions = structuredDocumentRegion.getRegions();
		String scope = "local"; //$NON-NLS-1$
		ITextRegion hrefRegion = null;

		int errorCount = 0;
		for (int i = 0; (i < textRegions.size())
				&& (errorCount < AbstractStructuredTextReconcilingStrategy.ELEMENT_ERROR_LIMIT); i++) {
			ITextRegion textRegion = textRegions.get(i);

			if (textRegion.getType() == DOMRegionContext.XML_TAG_ATTRIBUTE_VALUE) {
				String name = structuredDocumentRegion.getText(textRegions.get(i - 2));

				if ("href".equals(name)) { //$NON-NLS-1$
					hrefRegion = textRegions.get(i);
				} else if ("scope".equals(name)) { //$NON-NLS-1$
					String value = structuredDocumentRegion.getText(textRegions.get(i));
					scope = unquote(value);
				} else if ("conref".equals(name)) { //$NON-NLS-1$
					validateReference(structuredDocumentRegion, textRegion,
							"topic", reporter, file); //$NON-NLS-1$
				} else if ("mapref".equals(name)) { //$NON-NLS-1$
					validateReference(structuredDocumentRegion, textRegion,
							"map", reporter, file); //$NON-NLS-1$
				}
			}
		}

		if (hrefRegion != null) {
			if (scope.equals("local")) { //$NON-NLS-1$
				validateReference(structuredDocumentRegion, hrefRegion,
						"topic", reporter, file); //$NON-NLS-1$
			}
		}

	}

	private void validateReference(
			IStructuredDocumentRegion structuredDocumentRegion,
			ITextRegion textRegion, String type, IReporter reporter, IFile file) {
		try {
			String href = unquote(structuredDocumentRegion.getText(textRegion));
			URI target = new URI(href);

			if (!target.isAbsolute()) {
				// TODO: Should validate presence of ID when there
				// is a fragment in the URL
				IFile targetFile = file.getParent().getFile(
						new Path(target.getPath()));

				if (!targetFile.exists()) {
					int start = structuredDocumentRegion.getStartOffset(textRegion) + 1;
					int textLength = structuredDocumentRegion.getText(
							textRegion).trim().length() - 2;
					int lineNo = getLineNumber(start);

					String messageText = Messages.getString(
							"TargetValidator.missingTarget", href); //$NON-NLS-1$

					try {
						IMarker marker = file.createMarker(MISSING_REF_TARGET_MARKER);
						marker.setAttribute(IMarker.MESSAGE, messageText);
						marker.setAttribute(IMarker.SEVERITY,
								IMarker.SEVERITY_ERROR);
						marker.setAttribute(IMarker.LINE_NUMBER, lineNo);
						marker.setAttribute(IMarker.CHAR_START, start);
						marker.setAttribute(IMarker.CHAR_END, start
								+ textLength);

						marker.setAttribute("href", href); //$NON-NLS-1$
						marker.setAttribute("type", type); //$NON-NLS-1$
						return;
					} catch (CoreException e) {
						Activator.getDefault().log(e.getStatus());
					}
				}
			}
		} catch (URISyntaxException e) {
		}
	}

	private String unquote(String str) {
		return str.substring(1, str.length() - 1);
	}

	private int getLineNumber(int start) {
		return document.getLineOfOffset(start);
	}

}
