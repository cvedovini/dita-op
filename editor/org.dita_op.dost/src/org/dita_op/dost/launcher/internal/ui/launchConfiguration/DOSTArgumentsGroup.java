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

import java.util.HashMap;
import java.util.Iterator;
import java.util.Map;

import org.eclipse.jface.viewers.ArrayContentProvider;
import org.eclipse.jface.viewers.ColumnLayoutData;
import org.eclipse.jface.viewers.ColumnWeightData;
import org.eclipse.jface.viewers.DoubleClickEvent;
import org.eclipse.jface.viewers.IDoubleClickListener;
import org.eclipse.jface.viewers.ISelectionChangedListener;
import org.eclipse.jface.viewers.IStructuredSelection;
import org.eclipse.jface.viewers.ITableLabelProvider;
import org.eclipse.jface.viewers.LabelProvider;
import org.eclipse.jface.viewers.SelectionChangedEvent;
import org.eclipse.jface.viewers.TableLayout;
import org.eclipse.jface.viewers.TableViewer;
import org.eclipse.jface.viewers.Viewer;
import org.eclipse.jface.viewers.ViewerComparator;
import org.eclipse.jface.window.Window;
import org.eclipse.swt.SWT;
import org.eclipse.swt.custom.BusyIndicator;
import org.eclipse.swt.events.KeyAdapter;
import org.eclipse.swt.events.KeyEvent;
import org.eclipse.swt.events.SelectionAdapter;
import org.eclipse.swt.events.SelectionEvent;
import org.eclipse.swt.graphics.Image;
import org.eclipse.swt.layout.GridData;
import org.eclipse.swt.layout.GridLayout;
import org.eclipse.swt.widgets.Button;
import org.eclipse.swt.widgets.Composite;
import org.eclipse.swt.widgets.Link;
import org.eclipse.swt.widgets.Table;
import org.eclipse.swt.widgets.TableColumn;
import org.eclipse.ui.PlatformUI;

@SuppressWarnings( { "unchecked" })//$NON-NLS-1$ //$NON-NLS-2$
public abstract class DOSTArgumentsGroup {

	private final Composite group;
	private Button editButton;
	private Button removeButton;
	private Button addButton;

	private TableViewer propertyTableViewer;

	private Map args = null;

	private final String[] tableColumnHeaders = {
			Messages.getString("DOSTArgumentsGroup.nameColumn.label"), Messages.getString("DOSTArgumentsGroup.valueColumn.label") }; //$NON-NLS-1$ //$NON-NLS-2$

	private final ColumnLayoutData[] tableColumnLayouts = {
			new ColumnWeightData(40), new ColumnWeightData(60) };

	/**
	 * Button listener that delegates for widget selection events.
	 */
	private SelectionAdapter buttonListener = new SelectionAdapter() {
		@Override
		public void widgetSelected(SelectionEvent event) {
			if (event.widget == addButton) {
				addArgument();
			} else if (event.widget == editButton) {
				editArgument();
			} else if (event.widget == removeButton) {
				removeArgument();
			}
		}
	};

	/**
	 * Key listener that delegates for key pressed events.
	 */
	private KeyAdapter keyListener = new KeyAdapter() {
		@Override
		public void keyPressed(KeyEvent event) {
			if (event.getSource() == propertyTableViewer) {
				if (removeButton.isEnabled() && event.character == SWT.DEL
						&& event.stateMask == 0) {
					removeArgument();
				}
			}
		}
	};

	/**
	 * Selection changed listener that delegates selection events.
	 */
	private ISelectionChangedListener tableListener = new ISelectionChangedListener() {
		public void selectionChanged(SelectionChangedEvent event) {
			if (event.getSource() == propertyTableViewer) {
				propertyTableSelectionChanged((IStructuredSelection) event.getSelection());
			}
		}
	};

	public DOSTArgumentsGroup(Composite parent) {
		group = new Composite(parent, SWT.NONE);
		group.setLayoutData(new GridData(GridData.FILL_BOTH));
		createControls(group);
	}

	public abstract void setDirty(boolean dirty);

	public void setArguments(Map arguments) {
		if (arguments == null) {
			args = new HashMap();
		} else {
			args = new HashMap(arguments);
		}

		propertyTableViewer.setInput(args.entrySet());
	}

	public Map getArguments() {
		return args;
	}

	private void createControls(Composite parent) {
		parent.setLayout(new GridLayout(2, false));

		Link link = new Link(parent, SWT.NONE);
		link.setText(Messages.getString("DOSTArgumentsGroup.linkToDoc")); //$NON-NLS-1$
		GridData data = new GridData(GridData.FILL_HORIZONTAL);
		data.horizontalSpan = 2;
		link.setLayoutData(data);
		link.addSelectionListener(new SelectionAdapter() {
			public void widgetSelected(final SelectionEvent e) {
				BusyIndicator.showWhile(group.getDisplay(), new Runnable() {
					public void run() {
						PlatformUI.getWorkbench().getHelpSystem().displayHelpResource(
								e.text);
					}
				});
			}
		});

		propertyTableViewer = createTableViewer(parent);
		propertyTableViewer.addDoubleClickListener(new IDoubleClickListener() {
			public void doubleClick(DoubleClickEvent event) {
				if (!event.getSelection().isEmpty() && editButton.isEnabled()) {
					editArgument();
				}
			}
		});

		propertyTableViewer.getTable().addKeyListener(keyListener);
		createButtonGroup(parent);
	}

	/**
	 * Creates the group which will contain the buttons.
	 */
	private void createButtonGroup(Composite parent) {
		Composite buttonGroup = new Composite(parent, SWT.NONE);
		GridLayout layout = new GridLayout();
		layout.marginHeight = 0;
		layout.marginWidth = 0;
		buttonGroup.setLayout(layout);
		buttonGroup.setLayoutData(new GridData(GridData.FILL_VERTICAL
				| GridData.HORIZONTAL_ALIGN_FILL));
		buttonGroup.setFont(parent.getFont());

		addButtonsToButtonGroup(buttonGroup);
	}

	/**
	 * Creates and returns a configured table viewer in the given parent
	 */
	private TableViewer createTableViewer(Composite parent) {
		Table table = new Table(parent, SWT.MULTI | SWT.FULL_SELECTION
				| SWT.BORDER);

		table.setLayoutData(new GridData(GridData.FILL_BOTH));

		TableViewer tableViewer = new TableViewer(table);
		final EntryLabelProvider labelProvider = new EntryLabelProvider();
		tableViewer.setLabelProvider(labelProvider);
		tableViewer.setContentProvider(new ArrayContentProvider());
		tableViewer.addSelectionChangedListener(tableListener);
		tableViewer.setComparator(new ViewerComparator() {
			@Override
			public int compare(Viewer viewer, Object e1, Object e2) {
				return labelProvider.getColumnText(e1, 0).compareToIgnoreCase(
						labelProvider.getColumnText(e2, 0));
			}
		});

		TableLayout tableLayout = new TableLayout();
		table.setLayout(tableLayout);
		table.setHeaderVisible(true);
		table.setLinesVisible(true);

		for (int i = 0; i < tableColumnHeaders.length; i++) {
			tableLayout.addColumnData(tableColumnLayouts[i]);
			TableColumn column = new TableColumn(table, SWT.NONE, i);
			column.setResizable(tableColumnLayouts[i].resizable);
			column.setText(tableColumnHeaders[i]);
		}

		return tableViewer;
	}

	private void propertyTableSelectionChanged(IStructuredSelection newSelection) {
		int size = newSelection.size();
		editButton.setEnabled(size == 1);
		removeButton.setEnabled(size > 0);
	}

	private void addButtonsToButtonGroup(Composite parent) {
		addButton = createPushButton(parent,
				Messages.getString("DOSTArgumentsGroup.addButton.label")); //$NON-NLS-1$
		editButton = createPushButton(parent,
				Messages.getString("DOSTArgumentsGroup.editButton.label")); //$NON-NLS-1$
		removeButton = createPushButton(parent,
				Messages.getString("DOSTArgumentsGroup.removeButton.label")); //$NON-NLS-1$
	}

	private Button createPushButton(Composite parent, String label) {
		Button button = new Button(parent, SWT.PUSH);
		button.setText(label);
		button.addSelectionListener(buttonListener);
		GridData gridData = new GridData(GridData.VERTICAL_ALIGN_BEGINNING
				| GridData.FILL_HORIZONTAL);
		button.setLayoutData(gridData);
		return button;
	}

	private void removeArgument() {
		IStructuredSelection sel = (IStructuredSelection) propertyTableViewer.getSelection();
		Iterator it = sel.iterator();

		while (it.hasNext()) {
			Map.Entry entry = (Map.Entry) it.next();
			args.remove(entry.getKey());
		}

		propertyTableViewer.refresh();
		setDirty(true);
	}

	private void addArgument() {
		String title = Messages.getString("DOSTArgumentsGroup.addArgumentDialog.title"); //$NON-NLS-1$
		AddArgumentDialog dialog = new AddArgumentDialog(group.getShell(),
				title, new String[] { "", "" }); //$NON-NLS-1$ //$NON-NLS-2$

		if (dialog.open() == Window.OK) {
			String[] pair = dialog.getNameValuePair();
			args.put(pair[0], pair[1]);
			propertyTableViewer.refresh();
			setDirty(true);
		}
	}

	private void editArgument() {
		IStructuredSelection selection = (IStructuredSelection) propertyTableViewer.getSelection();
		Map.Entry orig = (Map.Entry) selection.getFirstElement();

		String title = Messages.getString("DOSTArgumentsGroup.editArgumentDialog.title"); //$NON-NLS-1$
		AddArgumentDialog dialog = new AddArgumentDialog(
				propertyTableViewer.getControl().getShell(), title,
				new String[] { orig.getKey().toString(),
						orig.getValue().toString() });

		if (dialog.open() == Window.OK) {
			String[] pair = dialog.getNameValuePair();
			args.remove(orig.getKey());
			args.put(pair[0], pair[1]);
			propertyTableViewer.refresh();
			setDirty(true);
		}
	}

	private static class EntryLabelProvider extends LabelProvider implements
			ITableLabelProvider {

		public Image getColumnImage(Object element, int columnIndex) {
			return null;
		}

		public String getColumnText(Object element, int columnIndex) {
			Map.Entry entry = (Map.Entry) element;
			return (columnIndex == 0) ? entry.getKey().toString()
					: entry.getValue().toString();
		}

	}

}
