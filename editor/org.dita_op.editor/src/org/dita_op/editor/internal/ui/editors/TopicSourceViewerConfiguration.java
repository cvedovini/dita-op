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

import org.eclipse.jface.text.contentassist.IContentAssistProcessor;
import org.eclipse.jface.text.source.ISourceViewer;
import org.eclipse.wst.sse.core.text.IStructuredPartitions;
import org.eclipse.wst.xml.core.text.IXMLPartitions;
import org.eclipse.wst.xml.ui.StructuredTextViewerConfigurationXML;
import org.eclipse.wst.xml.ui.internal.contentassist.NoRegionContentAssistProcessor;

@SuppressWarnings("restriction") //$NON-NLS-1$
public class TopicSourceViewerConfiguration extends
		StructuredTextViewerConfigurationXML {

	public TopicSourceViewerConfiguration() {
	}

	protected IContentAssistProcessor[] getContentAssistProcessors(
			ISourceViewer sourceViewer, String partitionType) {
		IContentAssistProcessor[] processors = null;

		if ((partitionType == IStructuredPartitions.DEFAULT_PARTITION)
				|| (partitionType == IXMLPartitions.XML_DEFAULT)) {
			processors = new IContentAssistProcessor[] { new TopicContentAssistProcessor() };
		} else if (partitionType == IStructuredPartitions.UNKNOWN_PARTITION) {
			processors = new IContentAssistProcessor[] { new NoRegionContentAssistProcessor() };
		}

		return processors;
	}

}
