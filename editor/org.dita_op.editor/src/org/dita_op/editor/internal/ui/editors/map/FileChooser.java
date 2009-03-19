package org.dita_op.editor.internal.ui.editors.map;

import java.net.URI;

import org.dita_op.core.ui.dialogs.FileSelectionDialog;
import org.dita_op.editor.internal.Utils;
import org.eclipse.core.resources.IFile;
import org.eclipse.core.resources.ResourcesPlugin;
import org.eclipse.jface.viewers.IStructuredSelection;
import org.eclipse.swt.SWT;
import org.eclipse.swt.events.ModifyListener;
import org.eclipse.swt.events.SelectionAdapter;
import org.eclipse.swt.events.SelectionEvent;
import org.eclipse.swt.layout.GridData;
import org.eclipse.swt.layout.GridLayout;
import org.eclipse.swt.widgets.Button;
import org.eclipse.swt.widgets.Composite;
import org.eclipse.swt.widgets.Control;
import org.eclipse.swt.widgets.Text;
import org.eclipse.ui.forms.widgets.FormToolkit;

public class FileChooser {

	private final Composite control;
	private final Text text;
	private final Button button;
	private final URI baseLocation;
	private String description = "";

	public FileChooser(Composite parent, URI baseLocation, FormToolkit toolkit) {
		this.baseLocation = baseLocation;
		GridLayout layout = new GridLayout(2, false);
		layout.marginHeight = layout.marginWidth = 0;

		control = toolkit.createComposite(parent);
		control.setLayout(layout);

		text = toolkit.createText(control, "");
		text.setLayoutData(new GridData(GridData.FILL_HORIZONTAL));

		button = toolkit.createButton(control, "...", SWT.NONE);
		button.addSelectionListener(new SelectionAdapter() {

			/**
			 * @see org.eclipse.swt.events.SelectionAdapter#widgetSelected(org.eclipse.swt.events.SelectionEvent)
			 */
			@Override
			public void widgetSelected(SelectionEvent e) {
				onBrowse();
			}
		});
	}

	public void setText(String text) {
		this.text.setText(text);
	}

	public String getText() {
		return text.getText();
	}

	public void addModifyListener(ModifyListener listener) {
		text.addModifyListener(listener);
	}

	public void removeModifyListener(ModifyListener listener) {
		text.removeModifyListener(listener);
	}

	private void onBrowse() {
		FileSelectionDialog dialog = new FileSelectionDialog(button.getShell(),
				ResourcesPlugin.getWorkspace().getRoot(), description);

		dialog.open();
		IStructuredSelection result = dialog.getResult();

		if (result != null) {
			Object selection = result.getFirstElement();

			if (selection instanceof IFile) {
				IFile target = (IFile) selection;
				URI targetURI;

				if (baseLocation != null) {
					targetURI = URI.create(target.getFullPath().toString());
					targetURI = Utils.relativize(targetURI, baseLocation);
				} else {
					targetURI = target.getLocationURI();
				}

				text.setText(targetURI.toString());
			}
		}
	}

	public Control getControl() {
		return control;
	}

	public void setFocus() {
		text.setFocus();
	}

}
