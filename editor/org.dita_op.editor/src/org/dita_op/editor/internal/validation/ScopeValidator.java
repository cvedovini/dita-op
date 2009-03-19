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

import org.eclipse.core.resources.IFile;
import org.eclipse.jface.text.IDocument;
import org.eclipse.jface.text.IRegion;
import org.eclipse.wst.sse.core.StructuredModelManager;
import org.eclipse.wst.sse.core.internal.provisional.IStructuredModel;
import org.eclipse.wst.sse.core.internal.provisional.text.IStructuredDocument;
import org.eclipse.wst.sse.core.internal.provisional.text.IStructuredDocumentRegion;
import org.eclipse.wst.sse.core.internal.provisional.text.ITextRegion;
import org.eclipse.wst.sse.core.internal.provisional.text.ITextRegionList;
import org.eclipse.wst.sse.ui.internal.reconcile.AbstractStructuredTextReconcilingStrategy;
import org.eclipse.wst.sse.ui.internal.reconcile.validator.ISourceValidator;
import org.eclipse.wst.validation.internal.core.ValidationException;
import org.eclipse.wst.validation.internal.operations.LocalizedMessage;
import org.eclipse.wst.validation.internal.provisional.core.IMessage;
import org.eclipse.wst.validation.internal.provisional.core.IReporter;
import org.eclipse.wst.validation.internal.provisional.core.IValidationContext;
import org.eclipse.wst.validation.internal.provisional.core.IValidator;
import org.eclipse.wst.xml.core.internal.regions.DOMRegionContext;
import org.eclipse.wst.xml.core.internal.validation.core.Helper;

@SuppressWarnings("restriction")//$NON-NLS-1$
public class ScopeValidator implements IValidator, ISourceValidator {

	private IStructuredDocument document;

	public void connect(IDocument document) {
		if (document instanceof IStructuredDocument) {
			this.document = (IStructuredDocument) document;
		} else {
			this.document = null;
		}
	}

	public void disconnect(IDocument document) {
		this.document = null;
	}

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

	public void validate(IRegion dirtyRegion, IValidationContext helper,
			IReporter reporter) {
		reporter.removeAllMessages(this);

		IStructuredDocumentRegion[] regions = document.getStructuredDocumentRegions(
				dirtyRegion.getOffset(), dirtyRegion.getLength());
		for (int i = 0; i < regions.length && !reporter.isCancelled(); i++) {
			validate(regions[i], reporter, null);
		}
	}

	private void validate(IStructuredDocumentRegion structuredDocumentRegion,
			IReporter reporter, IFile file) {

		if (structuredDocumentRegion == null) {
			return;
		}

		if (isStartTag(structuredDocumentRegion)) {
			checkTargetScope(structuredDocumentRegion, reporter, file);
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

	private void checkTargetScope(
			IStructuredDocumentRegion structuredDocumentRegion,
			IReporter reporter, IFile file) {

		if (structuredDocumentRegion.isDeleted()) {
			return;
		}

		ITextRegionList textRegions = structuredDocumentRegion.getRegions();
		String href = null;
		String scope = "local"; //$NON-NLS-1$
		ITextRegion hrefRegion = null;

		int errorCount = 0;
		for (int i = 0; (i < textRegions.size())
				&& (errorCount < AbstractStructuredTextReconcilingStrategy.ELEMENT_ERROR_LIMIT); i++) {
			ITextRegion textRegion = textRegions.get(i);

			if (textRegion.getType() == DOMRegionContext.XML_TAG_ATTRIBUTE_VALUE) {
				String name = structuredDocumentRegion.getText(textRegions.get(i - 2));
				String value = structuredDocumentRegion.getText(textRegions.get(i));

				if ("href".equals(name)) { //$NON-NLS-1$
					href = unquote(value);
					hrefRegion = textRegions.get(i);
				} else if ("scope".equals(name)) { //$NON-NLS-1$
					scope = unquote(value);
				}
			}
		}

		if (href != null) {
			if (scope.equals("local")) { //$NON-NLS-1$
				try {
					URI target = new URI(href);

					if (target.isAbsolute()) {
						String messageText = Messages.getString(
								"ScopeValidator.invalidScope", href); //$NON-NLS-1$

						int start = structuredDocumentRegion.getStartOffset(hrefRegion) + 1;
						int lineNo = getLineNumber(start);
						int textLength = structuredDocumentRegion.getText(
								hrefRegion).trim().length() - 2;

						LocalizedMessage message = new LocalizedMessage(
								IMessage.NORMAL_SEVERITY, messageText, file);
						message.setOffset(start);
						message.setLength(textLength);
						message.setLineNo(lineNo);

						reporter.addMessage(this, message);
					}
				} catch (URISyntaxException e) {
				}
			}
		}

	}

	private String unquote(String str) {
		return str.substring(1, str.length() - 1);
	}

	private int getLineNumber(int start) {
		return document.getLineOfOffset(start);
	}

}
