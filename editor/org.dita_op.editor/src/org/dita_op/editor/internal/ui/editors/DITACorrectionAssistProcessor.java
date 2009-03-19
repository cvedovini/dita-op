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
package org.dita_op.editor.internal.ui.editors;

import java.util.ArrayList;
import java.util.Iterator;
import java.util.List;

import org.eclipse.core.resources.IMarker;
import org.eclipse.jface.text.IDocument;
import org.eclipse.jface.text.Position;
import org.eclipse.jface.text.contentassist.ICompletionProposal;
import org.eclipse.jface.text.contentassist.IContextInformation;
import org.eclipse.jface.text.quickassist.IQuickAssistInvocationContext;
import org.eclipse.jface.text.quickassist.IQuickAssistProcessor;
import org.eclipse.jface.text.source.Annotation;
import org.eclipse.jface.text.source.IAnnotationModel;
import org.eclipse.swt.graphics.Image;
import org.eclipse.swt.graphics.Point;
import org.eclipse.ui.IMarkerResolution;
import org.eclipse.ui.IMarkerResolution2;
import org.eclipse.ui.ide.IDE;
import org.eclipse.ui.texteditor.SimpleMarkerAnnotation;

public class DITACorrectionAssistProcessor implements IQuickAssistProcessor {

	public DITACorrectionAssistProcessor() {
	}

	public boolean canAssist(IQuickAssistInvocationContext invocationContext) {
		return false;
	}

	public boolean canFix(Annotation annotation) {
		if (!(annotation instanceof SimpleMarkerAnnotation))
			return false;

		IMarker marker = ((SimpleMarkerAnnotation) annotation).getMarker();
		return IDE.getMarkerHelpRegistry().hasResolutions(marker);
	}

	@SuppressWarnings("unchecked") //$NON-NLS-1$
	public ICompletionProposal[] computeQuickAssistProposals(
			IQuickAssistInvocationContext invocationContext) {
		IAnnotationModel model = invocationContext.getSourceViewer().getAnnotationModel();

		int offset = invocationContext.getOffset();
		Iterator it = model.getAnnotationIterator();
		List list = new ArrayList();

		while (it.hasNext()) {
			Object key = it.next();
			if (!(key instanceof SimpleMarkerAnnotation))
				continue;

			SimpleMarkerAnnotation annotation = (SimpleMarkerAnnotation) key;
			IMarker marker = annotation.getMarker();
			IMarkerResolution[] mapping = IDE.getMarkerHelpRegistry().getResolutions(
					marker);

			if (mapping != null) {
				Position pos = model.getPosition(annotation);

				int start = marker.getAttribute(IMarker.CHAR_START, 0);
				int end = marker.getAttribute(IMarker.CHAR_END, 0);

				if (offset >= start && offset <= end) {
					for (int i = 0; i < mapping.length; i++) {
						list.add(new CorrectionProposal(mapping[i], pos, marker));
					}
				}

			}
		}
		return (ICompletionProposal[]) list.toArray(new ICompletionProposal[list.size()]);
	}

	public String getErrorMessage() {
		return null;
	}

	private static class CorrectionProposal implements ICompletionProposal {

		Position position;
		IMarkerResolution resolution;
		IMarker marker;

		public CorrectionProposal(IMarkerResolution resolution, Position pos,
				IMarker marker) {
			this.position = pos;
			this.resolution = resolution;
			this.marker = marker;
		}

		public void apply(IDocument document) {
			resolution.run(marker);
		}

		public Point getSelection(IDocument document) {
			return new Point(position.offset, 0);
		}

		public String getAdditionalProposalInfo() {
			if (resolution instanceof IMarkerResolution2)
				return ((IMarkerResolution2) resolution).getDescription();

			return null;
		}

		public String getDisplayString() {
			return resolution.getLabel();
		}

		public Image getImage() {
			if (resolution instanceof IMarkerResolution2) {
				return ((IMarkerResolution2) resolution).getImage();
			}

			return null;
		}

		public IContextInformation getContextInformation() {
			return null;
		}

	}

}
