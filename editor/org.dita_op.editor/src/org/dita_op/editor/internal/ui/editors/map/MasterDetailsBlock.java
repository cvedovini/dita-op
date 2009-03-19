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
import org.eclipse.jface.action.Action;
import org.eclipse.jface.viewers.ISelectionProvider;
import org.eclipse.swt.SWT;
import org.eclipse.swt.layout.GridData;
import org.eclipse.swt.widgets.Composite;
import org.eclipse.ui.forms.DetailsPart;
import org.eclipse.ui.forms.IDetailsPage;
import org.eclipse.ui.forms.IDetailsPageProvider;
import org.eclipse.ui.forms.IManagedForm;
import org.eclipse.ui.forms.widgets.FormToolkit;
import org.eclipse.ui.forms.widgets.ScrolledForm;
import org.eclipse.ui.forms.widgets.Section;
import org.w3c.dom.Element;

class MasterDetailsBlock extends org.eclipse.ui.forms.MasterDetailsBlock {

	private MasterSection masterSection;

	public MasterDetailsBlock() {
		super();
	}

	@Override
	public void createContent(IManagedForm managedForm) {
		super.createContent(managedForm);
		managedForm.getForm().getBody().setLayout(
				FormLayoutFactory.createFormGridLayout(false, 1));
	}

	public void setBaseLocation(URI baseLocation) {
		masterSection.setBaseLocation(baseLocation);
	}

	@Override
	protected void createMasterPart(IManagedForm managedForm, Composite parent) {
		FormToolkit toolkit = managedForm.getToolkit();

		Composite container = toolkit.createComposite(parent);
		container.setLayout(FormLayoutFactory.createMasterGridLayout(false, 1));
		container.setLayoutData(new GridData(GridData.FILL_BOTH));

		masterSection = new MasterSection(container, toolkit);
		managedForm.addPart(masterSection);

		Section section = masterSection.getSection();
		section.setLayout(FormLayoutFactory.createClearGridLayout(false, 1));
		section.setLayoutData(new GridData(GridData.FILL_BOTH));
	}

	@Override
	protected void createToolBarActions(IManagedForm managedForm) {
		final ScrolledForm form = managedForm.getForm();
		Action haction = new Action("hor", Action.AS_RADIO_BUTTON) { //$NON-NLS-1$
			public void run() {
				sashForm.setOrientation(SWT.HORIZONTAL);
				form.reflow(true);
			}
		};
		haction.setChecked(true);
		haction.setToolTipText(Messages.getString("MasterDetailsBlock.horButton")); //$NON-NLS-1$
		haction.setImageDescriptor(Activator.getImageDescriptor(ImageConstants.IMG_HORIZONTAL));
		Action vaction = new Action("ver", Action.AS_RADIO_BUTTON) { //$NON-NLS-1$
			public void run() {
				sashForm.setOrientation(SWT.VERTICAL);
				form.reflow(true);
			}
		};
		vaction.setChecked(false);
		vaction.setToolTipText(Messages.getString("MasterDetailsBlock.verButton")); //$NON-NLS-1$
		vaction.setImageDescriptor(Activator.getImageDescriptor(ImageConstants.IMG_VERTICAL));
		form.getToolBarManager().add(haction);
		form.getToolBarManager().add(vaction);
	}

	@Override
	protected void registerPages(DetailsPart detailsPart) {
		detailsPart.setPageProvider(new DetailsPageProvider());
	}

	class DetailsPageProvider implements IDetailsPageProvider {

		public IDetailsPage getPage(Object key) {
			if ("topicgroup".equals(key)) { //$NON-NLS-1$
				return new TopicgroupDetails(masterSection);
			} else if ("topichead".equals(key)) { //$NON-NLS-1$
				return new TopicheadDetails(masterSection);
			} else if ("topicref".equals(key)) { //$NON-NLS-1$
				return new TopicrefDetails(masterSection);
			} else if ("navref".equals(key)) { //$NON-NLS-1$
				return new NavrefDetails(masterSection);
			} else if ("anchor".equals(key)) { //$NON-NLS-1$
				return new AnchorDetails(masterSection);
			} else if ("map".equals(key)) { //$NON-NLS-1$
				return new MapDetails(masterSection);
			} else {
				return null;
			}
		}

		public Object getPageKey(Object object) {
			Element element = (Element) object;
			return element.getLocalName();
		}

	}

	public ISelectionProvider getSelectionProvider() {
		return masterSection.getSelectionProvider();
	}
}