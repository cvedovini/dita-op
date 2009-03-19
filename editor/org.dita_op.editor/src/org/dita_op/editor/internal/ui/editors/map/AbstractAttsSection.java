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
package org.dita_op.editor.internal.ui.editors.map;

import org.dita_op.editor.internal.ui.editors.FormLayoutFactory;
import org.eclipse.swt.events.ModifyEvent;
import org.eclipse.swt.events.ModifyListener;
import org.eclipse.swt.events.SelectionEvent;
import org.eclipse.swt.events.SelectionListener;
import org.eclipse.swt.layout.GridData;
import org.eclipse.swt.widgets.Composite;
import org.eclipse.swt.widgets.Control;
import org.eclipse.ui.forms.AbstractFormPart;
import org.eclipse.ui.forms.events.ExpansionAdapter;
import org.eclipse.ui.forms.events.ExpansionEvent;
import org.eclipse.ui.forms.widgets.FormToolkit;
import org.eclipse.ui.forms.widgets.Section;
import org.w3c.dom.Element;

abstract class AbstractAttsSection implements ModifyListener, SelectionListener {

	private Section section;
	private AbstractFormPart form;

	public AbstractAttsSection(Composite parent, AbstractFormPart form,
			int style) {
		this.form = form;
		FormToolkit toolkit = form.getManagedForm().getToolkit();
		section = toolkit.createSection(parent, style);
		hookListeners();

		section.setLayoutData(new GridData(GridData.FILL_HORIZONTAL));

		section.clientVerticalSpacing = FormLayoutFactory.SECTION_HEADER_VERTICAL_SPACING;
		section.setClient(createClient(section, toolkit));
	}

	public AbstractAttsSection(Composite parent, AbstractFormPart form) {
		this(parent, form, Section.TITLE_BAR | Section.TWISTIE
				| Section.EXPANDED | Section.COMPACT);
	}

	public Section getSection() {
		return section;
	}

	protected void hookListeners() {
		if ((section.getExpansionStyle() & Section.TWISTIE) != 0
				|| (section.getExpansionStyle() & Section.TREE_NODE) != 0) {
			section.addExpansionListener(new ExpansionAdapter() {
				public void expansionStateChanging(ExpansionEvent e) {
				}

				public void expansionStateChanged(ExpansionEvent e) {
					form.getManagedForm().getForm().reflow(false);
				}
			});
		}
	}

	protected abstract Composite createClient(Composite parent,
			FormToolkit toolkit);

	protected abstract void load(Element model);

	protected abstract void save(Element model);

	public void markDirty() {
		form.markDirty();
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

	public void setFocus() {
		section.setExpanded(true);
		Control[] children = section.getChildren();

		if (children != null && children.length > 0) {
			children[0].setFocus();
		} else {
			section.setFocus();
		}
	}
}