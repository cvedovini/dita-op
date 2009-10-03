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
package org.dita_op.dost.launcher.internal.ui.launchConfiguration;

import org.dita_op.core.ui.dialogs.FileSelectionDialog;
import org.eclipse.core.resources.IFile;
import org.eclipse.core.resources.ResourcesPlugin;
import org.eclipse.core.runtime.IPath;
import org.eclipse.core.variables.IStringVariableManager;
import org.eclipse.core.variables.VariablesPlugin;
import org.eclipse.debug.ui.StringVariableSelectionDialog;
import org.eclipse.jface.action.Action;
import org.eclipse.jface.action.MenuManager;
import org.eclipse.jface.viewers.IStructuredSelection;
import org.eclipse.swt.SWT;
import org.eclipse.swt.events.SelectionAdapter;
import org.eclipse.swt.events.SelectionEvent;
import org.eclipse.swt.events.SelectionListener;
import org.eclipse.swt.graphics.Point;
import org.eclipse.swt.graphics.Rectangle;
import org.eclipse.swt.widgets.Button;
import org.eclipse.swt.widgets.Composite;
import org.eclipse.swt.widgets.DirectoryDialog;
import org.eclipse.swt.widgets.FileDialog;
import org.eclipse.swt.widgets.Menu;
import org.eclipse.swt.widgets.Text;
import org.eclipse.ui.dialogs.ContainerSelectionDialog;

public class MultiBrowseButton {

	private final Button browseButton;
	private final Text textField;
	private final boolean browseFolder;
	private String extension;
	private MenuManager menuManager;
	private String description = ""; //$NON-NLS-1$

	private SelectionListener selectionListener = new SelectionAdapter() {

		/**
		 * @see org.eclipse.swt.events.SelectionAdapter#widgetSelected(org.eclipse.swt.events.SelectionEvent)
		 */
		@Override
		public void widgetSelected(SelectionEvent e) {
			menuManager.update(true);
			Point point = new Point(e.x, e.y);
			Rectangle rectangle = browseButton.getBounds();
			point = new Point(rectangle.x, rectangle.y + rectangle.height);

			Composite parent = browseButton.getParent();
			Menu menu = menuManager.createContextMenu(parent);
			point = parent.toDisplay(point);
			menu.setLocation(point.x, point.y);
			menu.setVisible(true);
		}

	};

	public MultiBrowseButton(Composite parent, Text textField) {
		this(parent, textField, false);
	}

	public MultiBrowseButton(Composite parent, Text textField,
			boolean browseFolder) {
		browseButton = new Button(parent, SWT.PUSH);
		this.textField = textField;
		this.browseFolder = browseFolder;

		menuManager = new MenuManager();
		menuManager.add(new Action(
				Messages.getString("MultiBrowseButton.browseWorkspace")) { //$NON-NLS-1$
			@Override
			public void run() {
				browseWorkspace();
			}
		});
		menuManager.add(new Action(
				Messages.getString("MultiBrowseButton.browseFileSystem")) { //$NON-NLS-1$
			@Override
			public void run() {
				browseFileSystem();
			}
		});
		menuManager.add(new Action(
				Messages.getString("MultiBrowseButton.browseVariables")) { //$NON-NLS-1$
			@Override
			public void run() {
				browseVariables();
			}
		});

		browseButton.addSelectionListener(selectionListener);
	}

	public void setText(String text) {
		browseButton.setText(text);
	}

	public void setExtension(String extension) {
		this.extension = extension;
	}

	public void setDescription(String description) {
		this.description = description;
	}

	private void browseWorkspace() {
		if (browseFolder) {
			ContainerSelectionDialog containerDialog;
			containerDialog = new ContainerSelectionDialog(
					browseButton.getShell(),
					ResourcesPlugin.getWorkspace().getRoot(), false,
					description);
			containerDialog.open();
			Object[] resource = containerDialog.getResult();

			if (resource != null && resource.length > 0) {
				IPath path = (IPath) resource[0];
				textField.setText(newVariableExpression(
						"resource_loc", path.toString())); //$NON-NLS-1$
			}
		} else {
			FileSelectionDialog dialog;
			dialog = new FileSelectionDialog(browseButton.getShell(),
					ResourcesPlugin.getWorkspace().getRoot(), description);
			dialog.setFileFilter(".*\\." + extension, true); //$NON-NLS-1$
			dialog.open();
			IStructuredSelection result = dialog.getResult();

			if (result != null) {
				Object file = result.getFirstElement();

				if (file instanceof IFile) {
					IPath path = ((IFile) file).getFullPath();
					textField.setText(newVariableExpression(
							"resource_loc", path.toString())); //$NON-NLS-1$
				}
			}
		}
	}

	private void browseFileSystem() {
		if (browseFolder) {
			DirectoryDialog dialog = new DirectoryDialog(
					browseButton.getShell(), SWT.SAVE);
			dialog.setText(description);
			dialog.setFilterPath(textField.getText());
			String text = dialog.open();

			if (text != null) {
				textField.setText(text);
			}
		} else {
			FileDialog dialog = new FileDialog(browseButton.getShell(),
					SWT.NONE);
			dialog.setText(description);
			dialog.setFilterExtensions(new String[] { "*." + extension }); //$NON-NLS-1$
			dialog.setFileName(textField.getText());
			String text = dialog.open();

			if (text != null) {
				textField.setText(text);
			}
		}
	}

	private void browseVariables() {
		StringVariableSelectionDialog dialog = new StringVariableSelectionDialog(
				browseButton.getShell());
		dialog.setTitle(description);
		dialog.open();
		String variable = dialog.getVariableExpression();

		if (variable != null) {
			textField.insert(variable);
		}
	}

	/**
	 * Returns a new variable expression with the given variable and the given
	 * argument.
	 * 
	 * @see IStringVariableManager#generateVariableExpression(String, String)
	 */
	private String newVariableExpression(String varName, String arg) {
		return VariablesPlugin.getDefault().getStringVariableManager().generateVariableExpression(
				varName, arg);
	}

}
