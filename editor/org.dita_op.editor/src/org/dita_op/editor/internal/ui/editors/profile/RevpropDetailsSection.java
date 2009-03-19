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
import org.dita_op.editor.internal.ui.editors.profile.model.Revprop;
import org.dita_op.editor.internal.utils.RGBUtils;
import org.eclipse.jface.layout.GridDataFactory;
import org.eclipse.jface.preference.ColorSelector;
import org.eclipse.jface.util.IPropertyChangeListener;
import org.eclipse.jface.util.PropertyChangeEvent;
import org.eclipse.jface.viewers.ISelection;
import org.eclipse.jface.viewers.IStructuredSelection;
import org.eclipse.swt.SWT;
import org.eclipse.swt.events.ModifyEvent;
import org.eclipse.swt.events.ModifyListener;
import org.eclipse.swt.events.SelectionEvent;
import org.eclipse.swt.events.SelectionListener;
import org.eclipse.swt.graphics.RGB;
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

class RevpropDetailsSection extends AbstractFormPart implements IDetailsPage,
		ModifyListener, SelectionListener {

	private static final String BLANK = ""; //$NON-NLS-1$

	private Revprop sel;
	private Text changeBarText;
	private ColorSelector changeBarColorSelector;
	private Text valueText;
	private Combo actionCombo;
	private Combo styleCombo;
	private ColorPicker fgColorPicker;
	private ColorPicker bgColorPicker;
	private FlagSection startFlagSection;
	private FlagSection endFlagSection;
	private boolean initialized = false;

	public RevpropDetailsSection() {
	}

	public void createContents(Composite parent) {
		FormToolkit toolkit = getManagedForm().getToolkit();
		parent.setLayout(FormLayoutFactory.createDetailsGridLayout(false, 1));

		Section section = toolkit.createSection(parent, Section.TITLE_BAR);
		section.setText(Messages.getString("RevpropDetailsSection.title")); //$NON-NLS-1$
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
		parent.setLayout(new GridLayout(3, false));

		GridData layoutData = new GridData(GridData.FILL_HORIZONTAL);
		layoutData.horizontalSpan = 2;

		toolkit.createLabel(parent,
				Messages.getString("RevpropDetailsSection.action.label")); //$NON-NLS-1$
		actionCombo = new Combo(parent, SWT.READ_ONLY);
		actionCombo.setLayoutData(GridDataFactory.copyData(layoutData));
		toolkit.adapt(actionCombo, true, false);
		actionCombo.add(ActionConstants.INCLUDE);
		actionCombo.add(ActionConstants.EXCLUDE);
		actionCombo.add(ActionConstants.PASSTHROUGH);
		actionCombo.add(ActionConstants.FLAG);
		actionCombo.addSelectionListener(this);

		toolkit.createLabel(parent,
				Messages.getString("RevpropDetailsSection.value.label")); //$NON-NLS-1$
		valueText = toolkit.createText(parent, BLANK, SWT.NONE);
		valueText.setLayoutData(GridDataFactory.copyData(layoutData));
		valueText.addModifyListener(this);

		toolkit.createLabel(parent,
				Messages.getString("RevpropDetailsSection.changebar.label")); //$NON-NLS-1$
		changeBarText = toolkit.createText(parent, BLANK, SWT.NONE);
		changeBarText.setLayoutData(new GridData(GridData.FILL_HORIZONTAL));
		changeBarText.addModifyListener(this);

		changeBarColorSelector = new ColorSelector(parent);
		changeBarColorSelector.addListener(new IPropertyChangeListener() {
			public void propertyChange(PropertyChangeEvent event) {
				changeBarText.setText(RGBUtils.toString(changeBarColorSelector.getColorValue()));
			}
		});

		layoutData = new GridData(GridData.FILL_HORIZONTAL);
		layoutData.horizontalSpan = 3;

		fgColorPicker = new ColorPicker(parent, SWT.NONE);
		fgColorPicker.setText(Messages.getString("RevpropDetailsSection.fgcolor.label")); //$NON-NLS-1$
		fgColorPicker.addSelectionListener(this);
		fgColorPicker.setLayoutData(GridDataFactory.copyData(layoutData));
		toolkit.adapt(fgColorPicker, true, true);

		bgColorPicker = new ColorPicker(parent, SWT.NONE);
		bgColorPicker.setText(Messages.getString("RevpropDetailsSection.bgcolor.label")); //$NON-NLS-1$
		bgColorPicker.addSelectionListener(this);
		bgColorPicker.setLayoutData(GridDataFactory.copyData(layoutData));
		toolkit.adapt(bgColorPicker, true, true);

		toolkit.createLabel(parent,
				Messages.getString("RevpropDetailsSection.style.label")); //$NON-NLS-1$
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
		startFlagSection.setText(Messages.getString("RevpropDetailsSection.startflag.label")); //$NON-NLS-1$
		startFlagSection.addModifyListener(this);

		endFlagSection = new FlagSection(parent, toolkit);
		endFlagSection.setText(Messages.getString("RevpropDetailsSection.endflag.label")); //$NON-NLS-1$
		endFlagSection.setLayoutData(GridDataFactory.copyData(layoutData));
		endFlagSection.addModifyListener(this);
	}

	public void selectionChanged(IFormPart part, ISelection selection) {
		initialized = false;
		sel = (Revprop) ((IStructuredSelection) selection).getFirstElement();

		String changeBar = sel.getChangeBar();
		setText(changeBarText, changeBar);

		RGB changeBarRGB = RGBUtils.parse(changeBar);
		if (changeBarRGB != null) {
			changeBarColorSelector.setColorValue(changeBarRGB);
		} else {
			changeBarColorSelector.setColorValue(RGBUtils.SILVER);
		}

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
			if (e.getSource() == changeBarText) {
				String changeBar = normalize(changeBarText.getText());
				sel.setChangeBar(changeBar);

				initialized = false;
				RGB changeBarRGB = RGBUtils.parse(changeBar);
				if (changeBarRGB != null) {
					changeBarColorSelector.setColorValue(changeBarRGB);
				} else {
					changeBarColorSelector.setColorValue(RGBUtils.SILVER);
				}
				initialized = true;

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