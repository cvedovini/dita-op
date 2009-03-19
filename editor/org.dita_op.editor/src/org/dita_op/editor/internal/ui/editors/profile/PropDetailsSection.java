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
import org.dita_op.editor.internal.ui.editors.profile.model.ActionConstants;
import org.dita_op.editor.internal.ui.editors.profile.model.Prop;
import org.eclipse.jface.layout.GridDataFactory;
import org.eclipse.jface.viewers.ISelection;
import org.eclipse.jface.viewers.IStructuredSelection;
import org.eclipse.swt.SWT;
import org.eclipse.swt.events.ModifyEvent;
import org.eclipse.swt.events.ModifyListener;
import org.eclipse.swt.events.SelectionEvent;
import org.eclipse.swt.events.SelectionListener;
import org.eclipse.swt.layout.GridData;
import org.eclipse.swt.layout.GridLayout;
import org.eclipse.swt.widgets.Combo;
import org.eclipse.swt.widgets.Composite;
import org.eclipse.swt.widgets.Text;
import org.eclipse.ui.forms.AbstractFormPart;
import org.eclipse.ui.forms.IDetailsPage;
import org.eclipse.ui.forms.IFormPart;
import org.eclipse.ui.forms.widgets.FormToolkit;
import org.eclipse.ui.forms.widgets.Section;

class PropDetailsSection extends AbstractFormPart implements IDetailsPage,
		ModifyListener, SelectionListener {

	private static final String BLANK = ""; //$NON-NLS-1$

	private Prop sel;
	private Text attributeText;
	private Text valueText;
	private Combo actionCombo;
	private Combo styleCombo;
	private ColorPicker fgColorPicker;
	private ColorPicker bgColorPicker;
	private FlagSection startFlagSection;
	private FlagSection endFlagSection;
	private boolean initialized = false;

	public PropDetailsSection() {
	}

	public void createContents(Composite parent) {
		FormToolkit toolkit = getManagedForm().getToolkit();
		parent.setLayout(FormLayoutFactory.createDetailsGridLayout(false, 1));

		Section section = toolkit.createSection(parent, Section.TITLE_BAR);
		section.setText(Messages.getString("PropDetailsSection.title")); //$NON-NLS-1$
		section.clientVerticalSpacing = FormLayoutFactory.SECTION_HEADER_VERTICAL_SPACING;
		section.setLayoutData(new GridData(GridData.FILL_BOTH));
		section.setLayout(new GridLayout());

		Composite client = toolkit.createComposite(section);
		client.setLayoutData(new GridData(GridData.FILL_BOTH));

		createClientArea(client, toolkit);
		section.setClient(client);
		initialized = true;
	}

	protected void createClientArea(Composite parent, FormToolkit toolkit) {
		parent.setLayout(new GridLayout(2, false));

		toolkit.createLabel(parent,
				Messages.getString("PropDetailsSection.action.label")); //$NON-NLS-1$
		actionCombo = new Combo(parent, SWT.READ_ONLY);
		actionCombo.setLayoutData(new GridData(GridData.FILL_HORIZONTAL));
		toolkit.adapt(actionCombo, true, false);
		actionCombo.add(ActionConstants.INCLUDE);
		actionCombo.add(ActionConstants.EXCLUDE);
		actionCombo.add(ActionConstants.PASSTHROUGH);
		actionCombo.add(ActionConstants.FLAG);
		actionCombo.addSelectionListener(this);

		toolkit.createLabel(parent,
				Messages.getString("PropDetailsSection.attribute.label")); //$NON-NLS-1$
		attributeText = toolkit.createText(parent, BLANK, SWT.NONE);
		attributeText.setLayoutData(new GridData(GridData.FILL_HORIZONTAL));
		attributeText.addModifyListener(this);

		toolkit.createLabel(parent,
				Messages.getString("PropDetailsSection.value.label")); //$NON-NLS-1$
		valueText = toolkit.createText(parent, BLANK, SWT.NONE);
		valueText.setLayoutData(new GridData(GridData.FILL_HORIZONTAL));
		valueText.addModifyListener(this);

		GridData layoutData = new GridData(GridData.FILL_HORIZONTAL);
		layoutData.horizontalSpan = 2;

		fgColorPicker = new ColorPicker(parent, SWT.NONE);
		fgColorPicker.setText(Messages.getString("PropDetailsSection.fgcolor.label")); //$NON-NLS-1$
		fgColorPicker.addSelectionListener(this);
		fgColorPicker.setLayoutData(GridDataFactory.copyData(layoutData));
		toolkit.adapt(fgColorPicker, true, true);

		bgColorPicker = new ColorPicker(parent, SWT.NONE);
		bgColorPicker.setText(Messages.getString("PropDetailsSection.bgcolor.label")); //$NON-NLS-1$
		bgColorPicker.addSelectionListener(this);
		bgColorPicker.setLayoutData(GridDataFactory.copyData(layoutData));
		toolkit.adapt(bgColorPicker, true, true);

		toolkit.createLabel(parent,
				Messages.getString("PropDetailsSection.style.label")); //$NON-NLS-1$
		styleCombo = new Combo(parent, SWT.READ_ONLY);
		styleCombo.setLayoutData(new GridData(GridData.FILL_HORIZONTAL));
		toolkit.adapt(styleCombo, true, false);
		styleCombo.add(SytleConstants.NONE);
		styleCombo.add(SytleConstants.UNDERLINE);
		styleCombo.add(SytleConstants.DOUBLE_UNDERLINE);
		styleCombo.add(SytleConstants.ITALICS);
		styleCombo.add(SytleConstants.OVERLINE);
		styleCombo.add(SytleConstants.BOLD);
		styleCombo.addSelectionListener(this);

		// Spacer
		toolkit.createLabel(parent, ""); //$NON-NLS-1$

		startFlagSection = new FlagSection(parent, toolkit);
		startFlagSection.setLayoutData(GridDataFactory.copyData(layoutData));
		startFlagSection.setText(Messages.getString("PropDetailsSection.startflag.label")); //$NON-NLS-1$
		startFlagSection.addModifyListener(this);

		endFlagSection = new FlagSection(parent, toolkit);
		endFlagSection.setText(Messages.getString("PropDetailsSection.endflag.label")); //$NON-NLS-1$
		endFlagSection.setLayoutData(GridDataFactory.copyData(layoutData));
		endFlagSection.addModifyListener(this);
	}

	public void selectionChanged(IFormPart part, ISelection selection) {
		initialized = false;
		sel = (Prop) ((IStructuredSelection) selection).getFirstElement();

		setText(attributeText, sel.getAttribute());
		setText(valueText, sel.getValue());
		setText(actionCombo, sel.getAction(), ActionConstants.FLAG);
		setText(styleCombo, sel.getStyle(), SytleConstants.NONE);

		fgColorPicker.setSelection(sel.getColor());
		bgColorPicker.setSelection(sel.getBackColor());

		startFlagSection.setFlag(sel.getStartFlag());
		endFlagSection.setFlag(sel.getEndFlag());
		initialized = true;
	}

	public void modifyText(ModifyEvent e) {
		if (initialized) {
			if (e.getSource() == attributeText) {
				sel.setAttribute(normalize(attributeText.getText()));
				markDirty();
			} else if (e.getSource() == valueText) {
				sel.setValue(normalize(valueText.getText()));
				markDirty();
			} else if (e.getSource() == startFlagSection.getSection()) {
				sel.setStartFlag(startFlagSection.getFlag());
				markDirty();
			} else if (e.getSource() == endFlagSection.getSection()) {
				sel.setEndFlag(endFlagSection.getFlag());
				markDirty();
			}
		}
	}

	public void widgetDefaultSelected(SelectionEvent e) {
		widgetSelected(e);
	}

	public void widgetSelected(SelectionEvent e) {
		if (initialized) {
			if (e.getSource() == actionCombo) {
				sel.setAction(normalize(actionCombo.getText()));
				markDirty();
			} else if (e.getSource() == styleCombo) {
				sel.setStyle(normalize(styleCombo.getText()));
				markDirty();
			} else if (e.getSource() == fgColorPicker) {
				sel.setColor(fgColorPicker.getSelection());
				markDirty();
			} else if (e.getSource() == bgColorPicker) {
				sel.setBackColor(bgColorPicker.getSelection());
				markDirty();
			}
		}
	}

	private void setText(Text control, String text) {
		control.setText(text == null ? BLANK : text);
	}

	private void setText(Combo control, String text, String deflt) {
		control.setText(text == null ? deflt : text);
	}

	private String normalize(String text) {
		text = text.trim();
		return (text.equals(BLANK) || text.equals(SytleConstants.NONE)) ? null
				: text;
	}
}