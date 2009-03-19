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

import java.beans.PropertyChangeEvent;
import java.beans.PropertyChangeListener;
import java.util.ArrayList;
import java.util.Iterator;
import java.util.List;

import org.dita_op.editor.internal.Activator;
import org.dita_op.editor.internal.ImageConstants;
import org.dita_op.editor.internal.ui.editors.FormLayoutFactory;
import org.dita_op.editor.internal.ui.editors.profile.model.AbstractProp;
import org.dita_op.editor.internal.ui.editors.profile.model.ProfileModel;
import org.dita_op.editor.internal.ui.editors.profile.model.Prop;
import org.dita_op.editor.internal.ui.editors.profile.model.Revprop;
import org.eclipse.jface.action.Action;
import org.eclipse.jface.action.MenuManager;
import org.eclipse.jface.viewers.ArrayContentProvider;
import org.eclipse.jface.viewers.BaseLabelProvider;
import org.eclipse.jface.viewers.ISelectionChangedListener;
import org.eclipse.jface.viewers.IStructuredSelection;
import org.eclipse.jface.viewers.ITableLabelProvider;
import org.eclipse.jface.viewers.SelectionChangedEvent;
import org.eclipse.jface.viewers.StructuredSelection;
import org.eclipse.jface.viewers.TableViewer;
import org.eclipse.jface.viewers.Viewer;
import org.eclipse.swt.SWT;
import org.eclipse.swt.events.SelectionAdapter;
import org.eclipse.swt.events.SelectionEvent;
import org.eclipse.swt.events.SelectionListener;
import org.eclipse.swt.graphics.Image;
import org.eclipse.swt.graphics.Point;
import org.eclipse.swt.graphics.Rectangle;
import org.eclipse.swt.layout.GridData;
import org.eclipse.swt.layout.GridLayout;
import org.eclipse.swt.widgets.Button;
import org.eclipse.swt.widgets.Composite;
import org.eclipse.swt.widgets.Menu;
import org.eclipse.swt.widgets.Table;
import org.eclipse.ui.forms.SectionPart;
import org.eclipse.ui.forms.widgets.FormToolkit;
import org.eclipse.ui.forms.widgets.Section;

@SuppressWarnings("unchecked")//$NON-NLS-1$
class PropsMasterSection extends SectionPart {

	private Button addButton;
	private Button removeButton;

	private MenuManager menuManager = null;

	private TableViewer propsViewer;

	private PropContentProvider modelProvider = new PropContentProvider();

	private SelectionListener buttonListener = new SelectionAdapter() {

		/**
		 * @see org.eclipse.swt.events.SelectionAdapter#widgetSelected(org.eclipse.swt.events.SelectionEvent)
		 */
		@SuppressWarnings("unchecked")//$NON-NLS-1$
		@Override
		public void widgetSelected(SelectionEvent e) {
			if (e.widget == addButton) {
				menuManager.update(true);
				Point point = new Point(e.x, e.y);
				Rectangle rectangle = addButton.getBounds();
				point = new Point(rectangle.x, rectangle.y + rectangle.height);

				Composite parent = addButton.getParent();
				Menu menu = menuManager.createContextMenu(parent);
				point = parent.toDisplay(point);
				menu.setLocation(point.x, point.y);
				menu.setVisible(true);
			} else if (e.widget == removeButton) {
				modelProvider.remove();
				markDirty();
			}
		}

	};

	private final Action addRevpropAction = new Action(
			Messages.getString("PropsMasterSection.addProp.menu"), //$NON-NLS-1$
			Activator.getImageDescriptor(ImageConstants.ICON_REVPROP)) {

		@Override
		public void run() {
			modelProvider.add(new Revprop());
			markDirty();
		}

	};

	private final Action addPropAction = new Action(
			Messages.getString("PropsMasterSection.addRevprop.menu"), //$NON-NLS-1$
			Activator.getImageDescriptor(ImageConstants.ICON_PROP)) {

		@Override
		public void run() {
			modelProvider.add(new Prop());
			markDirty();
		}

	};

	public PropsMasterSection(Composite parent, FormToolkit toolkit) {
		super(parent, toolkit, Section.TITLE_BAR);
		Section section = getSection();
		section.setText(Messages.getString("PropsMasterSection.title")); //$NON-NLS-1$
		section.clientVerticalSpacing = FormLayoutFactory.SECTION_HEADER_VERTICAL_SPACING;
		section.setClient(createClient(section, toolkit));

		menuManager = new MenuManager();
		menuManager.add(addPropAction);
		menuManager.add(addRevpropAction);
	}

	private Composite createClient(Composite parent, FormToolkit toolkit) {
		Composite container = toolkit.createComposite(parent);
		container.setLayout(new GridLayout(2, false));
		container.setLayoutData(new GridData(GridData.FILL_BOTH));

		createViewer(container, toolkit);
		createButtons(container, toolkit);

		return container;
	}

	private void createViewer(Composite parent, FormToolkit toolkit) {
		Table table = toolkit.createTable(parent, SWT.MULTI);
		table.setLayoutData(new GridData(GridData.FILL_BOTH));
		propsViewer = new TableViewer(table);
		propsViewer.setContentProvider(modelProvider);
		propsViewer.setLabelProvider(new PropLabelProvider());
		propsViewer.addSelectionChangedListener(new ISelectionChangedListener() {

			public void selectionChanged(SelectionChangedEvent event) {
				removeButton.setEnabled(!event.getSelection().isEmpty());
				getManagedForm().fireSelectionChanged(PropsMasterSection.this,
						event.getSelection());
			}

		});
	}

	private void createButtons(Composite container, FormToolkit toolkit) {
		Composite buttons = toolkit.createComposite(container);
		buttons.setLayout(new GridLayout(1, false));
		buttons.setLayoutData(new GridData(GridData.VERTICAL_ALIGN_BEGINNING));

		addButton = toolkit.createButton(
				buttons,
				Messages.getString("PropsMasterSection.addflag.button"), SWT.NONE); //$NON-NLS-1$
		addButton.setLayoutData(new GridData(GridData.FILL_HORIZONTAL));
		addButton.addSelectionListener(buttonListener);

		removeButton = toolkit.createButton(
				buttons,
				Messages.getString("PropsMasterSection.remove.button"), SWT.NONE); //$NON-NLS-1$
		removeButton.setLayoutData(new GridData(GridData.FILL_HORIZONTAL));
		removeButton.addSelectionListener(buttonListener);
	}

	/**
	 * @see org.eclipse.ui.forms.AbstractFormPart#setFormInput(java.lang.Object)
	 */
	@Override
	public boolean setFormInput(Object input) {
		if (input instanceof ProfileModel) {
			ProfileModel model = (ProfileModel) input;

			if (model.getVal().getProps() == null) {
				model.getVal().setProps(new ArrayList<AbstractProp>());
			}

			propsViewer.setInput(model.getVal().getProps());
			Object first = propsViewer.getElementAt(0);

			if (first != null) {
				propsViewer.setSelection(new StructuredSelection(first), true);
			}

			return true;
		}

		return false;
	}

	private class PropContentProvider extends ArrayContentProvider {

		private TableViewer viewer;
		private List<AbstractProp> model;
		private PropertyChangeListener modelListener = new PropertyChangeListener() {

			public void propertyChange(PropertyChangeEvent evt) {
				viewer.refresh(evt.getSource());
				markDirty();
			}
		};

		public void add(AbstractProp p) {
			if (model != null) {
				model.add(p);
				viewer.add(p);
				viewer.setSelection(new StructuredSelection(p), true);
				p.addPropertyChangeListener(modelListener);
			}
		}

		public void remove() {
			if (model != null) {
				IStructuredSelection sel = (IStructuredSelection) viewer.getSelection();
				Iterator<AbstractProp> it = sel.iterator();

				while (it.hasNext()) {
					AbstractProp p = it.next();
					p.removePropertyChangeListener(modelListener);
					model.remove(p);
					viewer.remove(p);
				}
			}
		}

		/**
		 * @see org.eclipse.jface.viewers.ArrayContentProvider#dispose()
		 */
		@Override
		public void dispose() {
			unlisten();
			super.dispose();
		}

		/**
		 * @see org.eclipse.jface.viewers.ArrayContentProvider#inputChanged(org.eclipse.jface.viewers.Viewer,
		 *      java.lang.Object, java.lang.Object)
		 */
		@Override
		public void inputChanged(Viewer viewer, Object oldInput, Object newInput) {
			super.inputChanged(viewer, oldInput, newInput);

			unlisten();
			this.viewer = (TableViewer) viewer;
			model = (List<AbstractProp>) newInput;
			listen();
		}

		private void unlisten() {
			if (model != null) {
				Iterator<AbstractProp> it = model.iterator();

				while (it.hasNext()) {
					it.next().removePropertyChangeListener(modelListener);
				}
			}
		}

		private void listen() {
			if (model != null) {
				Iterator<AbstractProp> it = model.iterator();

				while (it.hasNext()) {
					it.next().addPropertyChangeListener(modelListener);
				}
			}
		}

	}

	private static class PropLabelProvider extends BaseLabelProvider implements
			ITableLabelProvider {

		public Image getColumnImage(Object element, int columnIndex) {
			if (columnIndex == 0) {
				if (element instanceof Prop) {
					return Activator.getDefault().getImage(
							ImageConstants.ICON_PROP);
				} else if (element instanceof Revprop) {
					return Activator.getDefault().getImage(
							ImageConstants.ICON_REVPROP);
				}
			}

			return null;
		}

		public String getColumnText(Object element, int columnIndex) {
			AbstractProp p = (AbstractProp) element;
			StringBuilder builder = new StringBuilder();

			if (p.getAttribute() != null) {
				builder.append(p.getAttribute());
			} else {
				builder.append('*');
			}

			builder.append('=');

			if (p.getValue() != null) {
				builder.append(p.getValue());
			} else {
				builder.append('*');
			}

			return builder.toString();
		}

	}
}
