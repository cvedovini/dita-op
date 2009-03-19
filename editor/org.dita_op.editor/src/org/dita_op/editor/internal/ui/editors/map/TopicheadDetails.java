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

import org.eclipse.jface.layout.GridDataFactory;
import org.eclipse.swt.layout.GridData;
import org.eclipse.swt.layout.GridLayout;
import org.eclipse.swt.widgets.Composite;
import org.eclipse.swt.widgets.Text;
import org.eclipse.ui.forms.widgets.FormToolkit;
import org.w3c.dom.Element;

class TopicheadDetails extends AbstractDetailsPage {

	private Text navTitleText;
	private IdAttsSection idAttsSection;
	private AbstractAttsSection topicRefAttsSection;
	private SelectionAttsSection selectionAttsSection;
	private LocalAttsSection localAttsSection;
	private final MasterSection masterSection;

	public TopicheadDetails(MasterSection masterSection) {
		super(Messages.getString("TopicheadDetails.title")); //$NON-NLS-1$
		this.masterSection = masterSection;
	}

	/**
	 * @see org.dita_op.editor.internal.ui.editors.map.AbstractDetailsPage#createClientArea(org.eclipse.swt.widgets.Composite,
	 *      org.eclipse.ui.forms.widgets.FormToolkit)
	 */
	@Override
	protected void createClientArea(Composite parent, FormToolkit toolkit) {
		parent.setLayout(new GridLayout(2, false));

		toolkit.createLabel(parent,
				Messages.getString("TopicheadDetails.title.label")); //$NON-NLS-1$
		navTitleText = toolkit.createText(parent,
				Messages.getString("TopicheadDetails.title.default")); //$NON-NLS-1$
		navTitleText.setLayoutData(new GridData(GridData.FILL_HORIZONTAL));
		navTitleText.addModifyListener(this);
	}

	/**
	 * @see org.dita_op.editor.internal.ui.editors.map.AbstractDetailsPage#addSections(org.eclipse.swt.widgets.Composite,
	 *      org.eclipse.ui.forms.widgets.FormToolkit)
	 */
	@Override
	protected void addSections(Composite parent, FormToolkit toolkit) {
		GridData data = new GridData(GridData.FILL_HORIZONTAL);
		idAttsSection = new IdAttsSection(parent, masterSection.getBaseLocation(),
				this);
		idAttsSection.getSection().setLayoutData(data);

		topicRefAttsSection = new TopicrefAttsSection(parent, this);
		topicRefAttsSection.getSection().setLayoutData(
				GridDataFactory.copyData(data));

		selectionAttsSection = new SelectionAttsSection(parent, this);
		selectionAttsSection.getSection().setLayoutData(
				GridDataFactory.copyData(data));

		localAttsSection = new LocalAttsSection(parent, this);
		localAttsSection.getSection().setLayoutData(
				GridDataFactory.copyData(data));
	}

	protected void load(Element model) {
		ModelUtils.loadText(model, navTitleText, "navtitle"); //$NON-NLS-1$
		idAttsSection.load(model);
		topicRefAttsSection.load(model);
		selectionAttsSection.load(model);
		localAttsSection.load(model);
	}

	protected void save(Element model) {
		ModelUtils.saveText(model, navTitleText, "navtitle"); //$NON-NLS-1$
		idAttsSection.save(model);
		topicRefAttsSection.save(model);
		selectionAttsSection.save(model);
		localAttsSection.save(model);
	}

	/**
	 * @see org.eclipse.ui.forms.AbstractFormPart#setFocus()
	 */
	@Override
	public void setFocus() {
		navTitleText.setFocus();
	}

}