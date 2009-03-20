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
package org.dita_op.editor.internal.ui.editors.map.pages;

import org.dita_op.editor.internal.ui.editors.FormLayoutFactory;
import org.dita_op.editor.internal.ui.editors.map.MasterSection;
import org.eclipse.jface.viewers.ISelection;
import org.eclipse.jface.viewers.IStructuredSelection;
import org.eclipse.swt.events.ModifyEvent;
import org.eclipse.swt.events.ModifyListener;
import org.eclipse.swt.events.SelectionEvent;
import org.eclipse.swt.events.SelectionListener;
import org.eclipse.swt.layout.GridData;
import org.eclipse.swt.layout.GridLayout;
import org.eclipse.swt.widgets.Composite;
import org.eclipse.ui.forms.AbstractFormPart;
import org.eclipse.ui.forms.IDetailsPage;
import org.eclipse.ui.forms.IFormPart;
import org.eclipse.ui.forms.widgets.FormToolkit;
import org.eclipse.ui.forms.widgets.Section;
import org.w3c.dom.Element;

public abstract class AbstractDetailsPage extends AbstractFormPart implements
		IDetailsPage, ModifyListener, SelectionListener {

	private Element model;
	private final String title;
	private boolean initialized = false;
	protected MasterSection masterSection;

	public AbstractDetailsPage(String title) {
		super();
		this.title = title;
	}

	public void setMasterSection(MasterSection masterSection) {
		this.masterSection = masterSection;
	}

	/**
	 * @return the title
	 */
	public String getTitle() {
		return title;
	}

	public void createContents(Composite parent) {
		FormToolkit toolkit = getManagedForm().getToolkit();
		parent.setLayout(FormLayoutFactory.createDetailsGridLayout(false, 1));

		Section section = toolkit.createSection(parent, Section.TITLE_BAR);
		section.setText(title);
		section.clientVerticalSpacing = FormLayoutFactory.SECTION_HEADER_VERTICAL_SPACING;
		section.setLayoutData(new GridData(GridData.FILL_HORIZONTAL));
		section.setLayout(new GridLayout());

		Composite client = toolkit.createComposite(section);
		client.setLayoutData(new GridData(GridData.FILL_HORIZONTAL));
		section.setClient(client);

		createClientArea(client, toolkit);
		addSections(parent, toolkit);
	}

	protected abstract void load(Element model);

	protected abstract void save(Element model);

	/**
	 * @see org.eclipse.ui.forms.IPartSelectionListener#selectionChanged(org.eclipse.ui.forms.IFormPart,
	 *      org.eclipse.jface.viewers.ISelection)
	 */
	public final void selectionChanged(IFormPart part, ISelection selection) {
		initialized = false;
		model = null;

		try {
			model = (Element) ((IStructuredSelection) selection).getFirstElement();
			load(model);
		} finally {
			initialized = true;
		}
	}

	/**
	 * @see org.eclipse.ui.forms.AbstractFormPart#refresh()
	 */
	@Override
	public final void refresh() {
		initialized = false;

		try {
			if (model != null) {
				load(model);
			}
		} finally {
			initialized = true;
		}
	}

	/**
	 * @see org.eclipse.ui.forms.AbstractFormPart#commit(boolean)
	 */
	@Override
	public final void commit(boolean onSave) {
		save(model);
		super.commit(onSave);
	}

	/**
	 * @see org.eclipse.ui.forms.AbstractFormPart#markDirty()
	 */
	@Override
	public final void markDirty() {
		if (model != null && initialized) {
			super.markDirty();
		}
	}

	public void modifyText(ModifyEvent e) {
		markDirty();
	}

	public void widgetDefaultSelected(SelectionEvent e) {
		markDirty();
	}

	public void widgetSelected(SelectionEvent e) {
		markDirty();
	}

	protected void createClientArea(Composite parent, FormToolkit toolkit) {
		parent.setLayout(new GridLayout());
	}

	protected void addSections(Composite parent, FormToolkit toolkit) {
	}

}