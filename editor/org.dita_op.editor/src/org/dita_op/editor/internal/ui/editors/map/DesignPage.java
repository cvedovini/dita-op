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
package org.dita_op.editor.internal.ui.editors.map;

import java.net.URI;

import org.dita_op.editor.internal.Activator;
import org.dita_op.editor.internal.ImageConstants;
import org.dita_op.editor.internal.ui.editors.FormLayoutFactory;
import org.eclipse.core.runtime.Platform;
import org.eclipse.jface.action.Action;
import org.eclipse.jface.action.IToolBarManager;
import org.eclipse.jface.text.IDocument;
import org.eclipse.jface.viewers.ISelectionProvider;
import org.eclipse.swt.custom.BusyIndicator;
import org.eclipse.swt.layout.GridData;
import org.eclipse.swt.widgets.Composite;
import org.eclipse.swt.widgets.Control;
import org.eclipse.swt.widgets.Display;
import org.eclipse.ui.PlatformUI;
import org.eclipse.ui.forms.ManagedForm;
import org.eclipse.ui.forms.widgets.FormToolkit;
import org.eclipse.ui.forms.widgets.ScrolledForm;
import org.eclipse.wst.sse.core.StructuredModelManager;
import org.eclipse.wst.sse.core.internal.provisional.IStructuredModel;
import org.eclipse.wst.xml.core.internal.provisional.document.IDOMModel;
import org.eclipse.wst.xml.ui.internal.tabletree.IDesignViewer;
import org.w3c.dom.Document;

@SuppressWarnings("restriction")//$NON-NLS-1$
class DesignPage implements IDesignViewer {

	private final ManagedForm mform;
	private final MasterDetailsBlock block;

	public DesignPage(Composite parent) {
		FormToolkit toolkit = createToolkit(parent.getDisplay());
		final ScrolledForm form = toolkit.createScrolledForm(parent);
		mform = new ManagedForm(toolkit, form);

		toolkit.decorateFormHeading(form.getForm());
		form.setText(getTitle());
		form.setImage(Activator.getDefault().getImage(
				ImageConstants.ICON_DITAMAP));

		// Display the help icon only if the langref plugin is present
		if (Platform.getBundle("org.dita_op.dita.langref") != null) { //$NON-NLS-1$
			IToolBarManager manager = form.getToolBarManager();
			Action helpAction = new Action("help") { //$NON-NLS-1$
				public void run() {
					BusyIndicator.showWhile(form.getForm().getDisplay(),
							new Runnable() {
								public void run() {
									PlatformUI.getWorkbench().getHelpSystem().displayHelpResource(
											"/org.dita_op.dita.langref/common/map2.html"); //$NON-NLS-1$
								}
							});
				}
			};
			helpAction.setToolTipText(Messages.getString("DesignPage.help.tooltip")); //$NON-NLS-1$
			helpAction.setImageDescriptor(Activator.getImageDescriptor(ImageConstants.ICON_HELP));
			manager.add(helpAction);
		}

		Composite body = form.getBody();
		body.setLayout(FormLayoutFactory.createFormGridLayout(false, 1));
		body.setLayoutData(new GridData(GridData.FILL_HORIZONTAL));

		block = new MasterDetailsBlock();
		block.createContent(mform);
	}

	public void setDocument(IDocument document) {
		/*
		 * let the text editor to be the one that manages the model's lifetime
		 */
		IStructuredModel model = null;
		try {
			model = StructuredModelManager.getModelManager().getExistingModelForRead(
					document);

			if ((model != null) && (model instanceof IDOMModel)) {
				Document domDoc = ((IDOMModel) model).getDocument();
				mform.setInput(domDoc);
				block.setBaseLocation(URI.create(model.getBaseLocation()));
			}
		} finally {
			if (model != null) {
				model.releaseFromRead();
			}
		}
	}

	public Control getControl() {
		return mform.getForm();
	}

	public ISelectionProvider getSelectionProvider() {
		return block.getSelectionProvider();
	}

	protected FormToolkit createToolkit(Display display) {
		// Create a toolkit that shares colors between editors.
		return new FormToolkit(Activator.getDefault().getFormColors(display));
	}

	public String getTitle() {
		return Messages.getString("DesignPage.title"); //$NON-NLS-1$
	}
}
