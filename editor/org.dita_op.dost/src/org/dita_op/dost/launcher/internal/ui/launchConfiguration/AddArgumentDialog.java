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
package org.dita_op.dost.launcher.internal.ui.launchConfiguration;

import org.eclipse.debug.ui.StringVariableSelectionDialog;
import org.eclipse.jface.dialogs.Dialog;
import org.eclipse.jface.dialogs.IDialogConstants;
import org.eclipse.swt.SWT;
import org.eclipse.swt.events.ModifyEvent;
import org.eclipse.swt.events.ModifyListener;
import org.eclipse.swt.events.SelectionAdapter;
import org.eclipse.swt.events.SelectionEvent;
import org.eclipse.swt.layout.GridData;
import org.eclipse.swt.layout.GridLayout;
import org.eclipse.swt.widgets.Button;
import org.eclipse.swt.widgets.Composite;
import org.eclipse.swt.widgets.Control;
import org.eclipse.swt.widgets.Label;
import org.eclipse.swt.widgets.Shell;
import org.eclipse.swt.widgets.Text;

public class AddArgumentDialog extends Dialog {

	private String name;
	private String value;

	private Text nameText;
	private Text valueText;

	private final String wndTitle;
	private final String[] initialValues;

	public AddArgumentDialog(Shell shell, String wndTitle,
			String[] initialValues) {
		super(shell);
		this.wndTitle = wndTitle;
		this.initialValues = initialValues;
	}

	/**
	 * @see org.eclipse.jface.dialogs.Dialog#createDialogArea(org.eclipse.swt.widgets.Composite)
	 */
	@Override
	protected Control createDialogArea(Composite parent) {
		Composite comp = (Composite) super.createDialogArea(parent);
		((GridLayout) comp.getLayout()).numColumns = 2;

		new Label(comp, SWT.NONE).setText(Messages.getString("AddArgumentDialog.nameText.label")); //$NON-NLS-1$

		nameText = new Text(comp, SWT.BORDER | SWT.SINGLE);
		nameText.setText(initialValues[0]);
		GridData gd = new GridData(GridData.FILL_HORIZONTAL);
		gd.widthHint = 300;
		nameText.setLayoutData(gd);
		nameText.addModifyListener(new ModifyListener() {
			public void modifyText(ModifyEvent e) {
				updateButtons();
			}
		});

		new Label(comp, SWT.NONE).setText(Messages.getString("AddArgumentDialog.valueText.label")); //$NON-NLS-1$

		valueText = new Text(comp, SWT.BORDER | SWT.SINGLE);
		valueText.setText(initialValues[1]);
		gd = new GridData(GridData.FILL_HORIZONTAL);
		gd.widthHint = 300;
		valueText.setLayoutData(gd);
		valueText.addModifyListener(new ModifyListener() {
			public void modifyText(ModifyEvent e) {
				updateButtons();
			}
		});

		Button variablesButton = new Button(comp, SWT.PUSH);
		variablesButton.setText(Messages.getString("AddArgumentDialog.variableButton.label")); //$NON-NLS-1$
		gd = new GridData(GridData.HORIZONTAL_ALIGN_END);
		gd.horizontalSpan = 2;
		int widthHint = convertHorizontalDLUsToPixels(IDialogConstants.BUTTON_WIDTH);
		gd.widthHint = Math.max(widthHint, variablesButton.computeSize(
				SWT.DEFAULT, SWT.DEFAULT, true).x);
		variablesButton.setLayoutData(gd);

		variablesButton.addSelectionListener(new SelectionAdapter() {
			@Override
			public void widgetSelected(SelectionEvent se) {
				getVariable();
			}
		});

		return comp;
	}

	protected void getVariable() {
		StringVariableSelectionDialog variablesDialog = new StringVariableSelectionDialog(
				getShell());

		if (variablesDialog.open() == IDialogConstants.OK_ID) {
			String variable = variablesDialog.getVariableExpression();

			if (variable != null) {
				valueText.insert(variable.trim());
			}
		}
	}

	/**
	 * Return the name/value pair entered in this dialog. If the cancel button
	 * was hit, both will be <code>null</code>.
	 */
	public String[] getNameValuePair() {
		return new String[] { name, value };
	}

	/**
	 * @see org.eclipse.jface.dialogs.Dialog#buttonPressed(int)
	 */
	@Override
	protected void buttonPressed(int buttonId) {
		if (buttonId == IDialogConstants.OK_ID) {
			name = nameText.getText().trim();
			value = valueText.getText();
		} else {
			name = null;
			value = null;
		}

		super.buttonPressed(buttonId);
	}

	/**
	 * @see org.eclipse.jface.window.Window#configureShell(org.eclipse.swt.widgets.Shell)
	 */
	@Override
	protected void configureShell(Shell shell) {
		super.configureShell(shell);

		if (wndTitle != null) {
			shell.setText(wndTitle);
		}
	}

	/**
	 * Enable the OK button if valid input
	 */
	protected void updateButtons() {
		String name = nameText.getText().trim();
		String value = valueText.getText().trim();

		getButton(IDialogConstants.OK_ID).setEnabled(
				(name.length() > 0) && (value.length() > 0));
	}

	/**
	 * Enable the buttons on creation.
	 * 
	 * @see org.eclipse.jface.window.Window#create()
	 */
	@Override
	public void create() {
		super.create();
		updateButtons();
	}
}
