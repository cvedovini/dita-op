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
package org.dita_op.editor.internal.ui.editors.map.pages;

import org.eclipse.jface.layout.GridDataFactory;
import org.eclipse.swt.layout.GridData;
import org.eclipse.swt.layout.GridLayout;
import org.eclipse.swt.widgets.Composite;
import org.eclipse.ui.forms.widgets.FormToolkit;
import org.w3c.dom.Element;

public class NavrefDetails extends AbstractDetailsPage {

	private FileChooser maprefText;
	private IdAttsSection idAttsSection;
	private SelectionAttsSection selectionAttsSection;
	private LocalAttsSection localAttsSection;

	public NavrefDetails() {
		super();
	}

	/**
	 * @see org.dita_op.editor.internal.ui.editors.map.pages.AbstractDetailsPage#createClientArea(org.eclipse.swt.widgets.Composite,
	 *      org.eclipse.ui.forms.widgets.FormToolkit)
	 */
	@Override
	protected void createClientArea(Composite parent, FormToolkit toolkit) {
		parent.setLayout(new GridLayout(2, false));

		toolkit.createLabel(parent,
				Messages.getString("NavrefDetails.mapref.label")); //$NON-NLS-1$
		maprefText = new FileChooser(parent, getBaseLocation(), toolkit);
		maprefText.getControl().setLayoutData(
				new GridData(GridData.FILL_HORIZONTAL));
		maprefText.addModifyListener(this);
	}

	/**
	 * @see org.dita_op.editor.internal.ui.editors.map.pages.AbstractDetailsPage#addSections(org.eclipse.swt.widgets.Composite,
	 *      org.eclipse.ui.forms.widgets.FormToolkit)
	 */
	@Override
	protected void addSections(Composite parent, FormToolkit toolkit) {
		GridData data = new GridData(GridData.FILL_HORIZONTAL);
		idAttsSection = new IdAttsSection(parent, this);
		idAttsSection.getSection().setLayoutData(data);

		selectionAttsSection = new SelectionAttsSection(parent, this);
		selectionAttsSection.getSection().setLayoutData(
				GridDataFactory.copyData(data));

		localAttsSection = new LocalAttsSection(parent, this);
		localAttsSection.getSection().setLayoutData(
				GridDataFactory.copyData(data));
	}

	protected void load(Element model) {
		ModelUtils.loadFile(model, maprefText, "mapref"); //$NON-NLS-1$
		idAttsSection.load(model);
		selectionAttsSection.load(model);
		localAttsSection.load(model);
	}

	protected Element save(Element model) {
		ModelUtils.saveFile(model, maprefText, "mapref"); //$NON-NLS-1$
		idAttsSection.save(model);
		selectionAttsSection.save(model);
		localAttsSection.save(model);

		return model;
	}

	/**
	 * @see org.eclipse.ui.forms.AbstractFormPart#setFocus()
	 */
	@Override
	public void setFocus() {
		maprefText.setFocus();
	}

}