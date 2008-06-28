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

import org.dita_op.editor.internal.ui.editors.model.Prop;
import org.dita_op.editor.internal.ui.editors.model.Revprop;
import org.eclipse.swt.layout.GridData;
import org.eclipse.swt.widgets.Composite;
import org.eclipse.ui.forms.DetailsPart;
import org.eclipse.ui.forms.IManagedForm;
import org.eclipse.ui.forms.MasterDetailsBlock;
import org.eclipse.ui.forms.SectionPart;
import org.eclipse.ui.forms.widgets.FormToolkit;
import org.eclipse.ui.forms.widgets.Section;

public class PropsMasterDetailsBlock extends MasterDetailsBlock {

	public PropsMasterDetailsBlock() {
		super();
	}

	@Override
	public void createContent(IManagedForm managedForm) {
		super.createContent(managedForm);
		managedForm.getForm().getBody().setLayout(
				FormLayoutFactory.createFormGridLayout(false, 1));
	}

	@Override
	protected void createMasterPart(IManagedForm managedForm, Composite parent) {
		FormToolkit toolkit = managedForm.getToolkit();

		Composite container = toolkit.createComposite(parent);
		container.setLayout(FormLayoutFactory.createMasterGridLayout(false, 1));
		container.setLayoutData(new GridData(GridData.FILL_BOTH));

		SectionPart part = new PropsMasterSection(container, toolkit);
		managedForm.addPart(part);

		Section section = part.getSection();
		section.setLayout(FormLayoutFactory.createClearGridLayout(false, 1));
		section.setLayoutData(new GridData(GridData.FILL_BOTH));
	}

	@Override
	protected void createToolBarActions(IManagedForm managedForm) {
	}

	@Override
	protected void registerPages(DetailsPart detailsPart) {
		detailsPart.registerPage(Prop.class, new PropDetailsSection());
		detailsPart.registerPage(Revprop.class, new RevpropDetailsSection());
	}

}