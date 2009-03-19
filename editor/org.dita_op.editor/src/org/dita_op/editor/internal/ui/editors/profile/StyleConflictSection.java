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
package org.dita_op.editor.internal.ui.editors.profile;

import org.dita_op.editor.internal.ui.editors.FormLayoutFactory;
import org.dita_op.editor.internal.ui.editors.profile.model.ProfileModel;
import org.eclipse.swt.SWT;
import org.eclipse.swt.events.SelectionAdapter;
import org.eclipse.swt.events.SelectionEvent;
import org.eclipse.swt.events.SelectionListener;
import org.eclipse.swt.graphics.RGB;
import org.eclipse.swt.layout.GridData;
import org.eclipse.swt.layout.GridLayout;
import org.eclipse.swt.widgets.Composite;
import org.eclipse.ui.forms.SectionPart;
import org.eclipse.ui.forms.widgets.FormToolkit;
import org.eclipse.ui.forms.widgets.Section;

class StyleConflictSection extends SectionPart {

	private ProfileModel model;
	private ColorPicker fgColorPicker;
	private ColorPicker bgColorPicker;
	private RGB foregroundColor;
	private RGB backgroundColor;

	private final SelectionListener listener = new SelectionAdapter() {

		public void widgetSelected(SelectionEvent event) {
			if (event.getSource() == fgColorPicker) {
				foregroundColor = fgColorPicker.getSelection();
				markDirty();
			} else if (event.getSource() == bgColorPicker) {
				backgroundColor = bgColorPicker.getSelection();
				markDirty();
			}
		}
	};

	public StyleConflictSection(Composite parent, FormToolkit toolkit) {
		super(parent, toolkit, Section.TITLE_BAR | Section.CLIENT_INDENT
				| Section.DESCRIPTION | Section.TWISTIE | Section.COMPACT);
		Section section = getSection();
		section.setDescription(Messages.getString("StyleConflictSection.description")); //$NON-NLS-1$

		section.setLayoutData(new GridData(GridData.FILL_HORIZONTAL));
		section.setText(Messages.getString("StyleConflictSection.title")); //$NON-NLS-1$
		section.clientVerticalSpacing = FormLayoutFactory.SECTION_HEADER_VERTICAL_SPACING;

		section.setClient(createClient(section, toolkit));
	}

	private Composite createClient(Composite parent, FormToolkit toolkit) {
		Composite container = toolkit.createComposite(parent);
		container.setLayout(new GridLayout());
		container.setLayoutData(new GridData(GridData.FILL_HORIZONTAL));

		fgColorPicker = new ColorPicker(container, SWT.NONE);
		fgColorPicker.setText(Messages.getString("StyleConflictSection.fgcolor.label")); //$NON-NLS-1$
		fgColorPicker.addSelectionListener(listener);
		toolkit.adapt(fgColorPicker, true, true);

		bgColorPicker = new ColorPicker(container, SWT.NONE);
		bgColorPicker.setText(Messages.getString("StyleConflictSection.bgcolor.label")); //$NON-NLS-1$
		bgColorPicker.addSelectionListener(listener);
		toolkit.adapt(bgColorPicker, true, true);

		return container;
	}

	/**
	 * @see org.eclipse.ui.forms.AbstractFormPart#commit(boolean)
	 */
	@Override
	public void commit(boolean onSave) {
		model.getVal().setForegroundConflictColor(foregroundColor);
		model.getVal().setBackgroundConflictColor(backgroundColor);

		super.commit(onSave);
	}

	/**
	 * @see org.eclipse.ui.forms.AbstractFormPart#setFormInput(java.lang.Object)
	 */
	@Override
	public boolean setFormInput(Object input) {
		if (input instanceof ProfileModel) {
			model = (ProfileModel) input;

			foregroundColor = model.getVal().getForegroundConflictColor();
			backgroundColor = model.getVal().getBackgroundConflictColor();

			fgColorPicker.setSelection(foregroundColor);
			bgColorPicker.setSelection(backgroundColor);

			return true;
		}

		return false;
	}

}
