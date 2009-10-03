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

import java.util.Iterator;

import org.dita_op.editor.internal.utils.DOMUtils;
import org.eclipse.jface.layout.GridDataFactory;
import org.eclipse.jface.viewers.IOpenListener;
import org.eclipse.jface.viewers.IStructuredSelection;
import org.eclipse.jface.viewers.OpenEvent;
import org.eclipse.swt.SWT;
import org.eclipse.swt.layout.GridData;
import org.eclipse.swt.layout.GridLayout;
import org.eclipse.swt.widgets.Composite;
import org.eclipse.swt.widgets.Text;
import org.eclipse.ui.forms.widgets.FormToolkit;
import org.w3c.dom.Element;

public class RelTableDetails extends AbstractDetailsPage {

	private Text titleText;
	private IdAttsSection idAttsSection;
	private TopicrefAttsSection topicRefAttsSection;
	private SelectionAttsSection selectionAttsSection;
	private LocalAttsSection localAttsSection;
	private Element workingCopy;
	private RelTableViewer viewer;

	public RelTableDetails() {
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
				Messages.getString("RelTableDetails.title.label")); //$NON-NLS-1$
		titleText = toolkit.createText(parent,
				Messages.getString("RelTableDetails.title.default")); //$NON-NLS-1$
		titleText.setLayoutData(new GridData(GridData.FILL_HORIZONTAL));
		titleText.addModifyListener(this);

		viewer = new RelTableViewer(parent, SWT.BORDER, getBaseLocation());
		viewer.setContentProvider(new RelTableContentProvider());
		viewer.setLabelProvider(new RelCellLabelProvider());

		GridData data = new GridData(GridData.FILL_BOTH);
		data.horizontalSpan = 2;
		data.minimumHeight = 200;
		viewer.getControl().setLayoutData(data);

		viewer.addOpenListener(new IOpenListener() {

			public void open(OpenEvent event) {
				IStructuredSelection sel = (IStructuredSelection) event.getSelection();
				Iterator it = sel.iterator();

				while (it.hasNext()) {
					DOMUtils.open(getBaseLocation(), (Element) it.next());
				}
			}
		});
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
		workingCopy = (Element) model.cloneNode(true);
		ModelUtils.loadText(workingCopy, titleText, "title"); //$NON-NLS-1$

		idAttsSection.load(workingCopy);
		topicRefAttsSection.load(workingCopy);
		selectionAttsSection.load(workingCopy);
		localAttsSection.load(workingCopy);

		viewer.setInput(workingCopy);
	}

	protected Element save(Element model) {
		ModelUtils.saveText(workingCopy, titleText, "title"); //$NON-NLS-1$

		idAttsSection.save(workingCopy);
		topicRefAttsSection.save(workingCopy);
		selectionAttsSection.save(workingCopy);
		localAttsSection.save(workingCopy);

		model.getParentNode().replaceChild(workingCopy, model);
		return workingCopy;
	}

	/**
	 * @see org.eclipse.ui.forms.AbstractFormPart#setFocus()
	 */
	@Override
	public void setFocus() {
		titleText.setFocus();
	}

}