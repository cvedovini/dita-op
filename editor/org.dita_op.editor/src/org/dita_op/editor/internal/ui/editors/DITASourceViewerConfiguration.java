/**
 *  Copyright (C) 2008 Claude Vedovini <http://vedovini.net/>.
 *
 *  This file is part of the DITA Open Platform <http://www.dita-op.org/>.
 *
 *  The DITA Open Platform is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The DITA Open Platform is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with The DITA Open Platform.  If not, see <http://www.gnu.org/licenses/>.
 */
package org.dita_op.editor.internal.ui.editors;

import org.eclipse.jface.preference.PreferenceConverter;
import org.eclipse.jface.text.DefaultInformationControl;
import org.eclipse.jface.text.IInformationControl;
import org.eclipse.jface.text.IInformationControlCreator;
import org.eclipse.jface.text.contentassist.IContentAssistProcessor;
import org.eclipse.jface.text.quickassist.IQuickAssistAssistant;
import org.eclipse.jface.text.quickassist.QuickAssistAssistant;
import org.eclipse.jface.text.source.ISourceViewer;
import org.eclipse.swt.SWT;
import org.eclipse.swt.graphics.Color;
import org.eclipse.swt.graphics.RGB;
import org.eclipse.swt.widgets.Shell;
import org.eclipse.wst.sse.core.text.IStructuredPartitions;
import org.eclipse.wst.sse.ui.internal.derived.HTMLTextPresenter;
import org.eclipse.wst.sse.ui.internal.preferences.EditorPreferenceNames;
import org.eclipse.wst.sse.ui.internal.util.EditorUtility;
import org.eclipse.wst.xml.core.text.IXMLPartitions;
import org.eclipse.wst.xml.ui.StructuredTextViewerConfigurationXML;
import org.eclipse.wst.xml.ui.internal.contentassist.NoRegionContentAssistProcessor;

@SuppressWarnings("restriction")//$NON-NLS-1$
public class DITASourceViewerConfiguration extends
		StructuredTextViewerConfigurationXML {

	public DITASourceViewerConfiguration() {
	}

	protected IContentAssistProcessor[] getContentAssistProcessors(
			ISourceViewer sourceViewer, String partitionType) {
		IContentAssistProcessor[] processors = null;

		if ((partitionType == IStructuredPartitions.DEFAULT_PARTITION)
				|| (partitionType == IXMLPartitions.XML_DEFAULT)) {
			processors = new IContentAssistProcessor[] { new DITAContentAssistProcessor() };
		} else if (partitionType == IStructuredPartitions.UNKNOWN_PARTITION) {
			processors = new IContentAssistProcessor[] { new NoRegionContentAssistProcessor() };
		}

		return processors;
	}

	private IQuickAssistAssistant fQuickAssistant;

	/**
	 * @see org.eclipse.wst.sse.ui.StructuredTextViewerConfiguration#getQuickAssistAssistant(org.eclipse.jface.text.source.ISourceViewer)
	 */
	@Override
	public IQuickAssistAssistant getQuickAssistAssistant(
			ISourceViewer sourceViewer) {
		// fQuickAssistant = super.getQuickAssistAssistant(sourceViewer);
		if (fQuickAssistant == null) {
			IQuickAssistAssistant assistant = new QuickAssistAssistant();
			assistant.setQuickAssistProcessor(new DITACorrectionAssistProcessor());
			assistant.setInformationControlCreator(new IInformationControlCreator() {
				public IInformationControl createInformationControl(Shell parent) {
					return new DefaultInformationControl(parent, SWT.NONE,
							new HTMLTextPresenter(true));
				}
			});

			// Waiting for color preferences, see:
			// https://bugs.eclipse.org/bugs/show_bug.cgi?id=133731
			// set content assist preferences
			if (fPreferenceStore != null) {
				Color color = getColor(EditorPreferenceNames.CODEASSIST_PROPOSALS_BACKGROUND);
				assistant.setProposalSelectorBackground(color);

				color = getColor(EditorPreferenceNames.CODEASSIST_PROPOSALS_FOREGROUND);
				assistant.setProposalSelectorForeground(color);
			}
			fQuickAssistant = assistant;
		}
		return fQuickAssistant;
	}

	private Color getColor(String key) {
		RGB rgb = PreferenceConverter.getColor(fPreferenceStore, key);
		return EditorUtility.getColor(rgb);
	}

}
