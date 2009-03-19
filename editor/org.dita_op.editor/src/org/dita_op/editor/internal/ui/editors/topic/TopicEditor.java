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
package org.dita_op.editor.internal.ui.editors.topic;

import java.io.IOException;
import java.net.URL;
import java.util.Calendar;

import javax.xml.transform.Templates;
import javax.xml.transform.Transformer;
import javax.xml.transform.TransformerException;

import org.dita_op.editor.internal.Activator;
import org.dita_op.editor.internal.ui.editors.XMLEditorWithHTMLPreview;
import org.eclipse.core.resources.IFile;
import org.eclipse.core.runtime.FileLocator;
import org.eclipse.ui.IFileEditorInput;
import org.osgi.framework.Bundle;

@SuppressWarnings("restriction") //$NON-NLS-1$
public class TopicEditor extends XMLEditorWithHTMLPreview {

	/**
	 * Creates a multi-page editor example.
	 */
	public TopicEditor() {
	}

	@Override
	protected Transformer createTransformer() throws IOException,
			TransformerException {
		Templates templates = Activator.getDefault().getPreviewTemplates().getTemplates(
				PreviewTemplates.TOPIC_PREVIEW_TEMPLATE);

		IFileEditorInput input = (IFileEditorInput) getEditorInput();
		IFile file = input.getFile();
		String baseLocation = file.getParent().getLocationURI().toString();

		Bundle bundle = Activator.getDefault().getBundle();
		URL css = bundle.getEntry("resource"); //$NON-NLS-1$
		css = FileLocator.resolve(css);

		Transformer transformer = templates.newTransformer();
		transformer.setParameter("CSSPATH", css.toString()); //$NON-NLS-1$
		transformer.setParameter("FILENAME", file.getName()); //$NON-NLS-1$
		transformer.setParameter("DITAEXT", "." + file.getFileExtension()); //$NON-NLS-1$ //$NON-NLS-2$
		transformer.setParameter("WORKDIR", baseLocation + "/"); //$NON-NLS-1$ //$NON-NLS-2$
		transformer.setParameter("YEAR", //$NON-NLS-1$
				Integer.toString(Calendar.getInstance().get(Calendar.YEAR)));

		return transformer;
	}

}
