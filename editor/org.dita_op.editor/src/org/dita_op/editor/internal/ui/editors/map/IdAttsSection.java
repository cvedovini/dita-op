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
package org.dita_op.editor.internal.ui.editors.map;

import java.net.URI;

import org.eclipse.swt.layout.GridData;
import org.eclipse.swt.layout.GridLayout;
import org.eclipse.swt.widgets.Composite;
import org.eclipse.swt.widgets.Text;
import org.eclipse.ui.forms.AbstractFormPart;
import org.eclipse.ui.forms.widgets.FormToolkit;
import org.w3c.dom.Element;

class IdAttsSection extends AbstractAttsSection {

	private Text idText;
	private FileChooser conrefText;
	private final URI baseLocation;

	public IdAttsSection(Composite parent, URI baseLocation,
			AbstractFormPart form) {
		super(parent, form);
		this.baseLocation = baseLocation;
		getSection().setText(Messages.getString("IdAttsSection.title")); //$NON-NLS-1$
	}

	protected Composite createClient(Composite parent, FormToolkit toolkit) {
		Composite container = toolkit.createComposite(parent);
		container.setLayout(new GridLayout(2, false));
		container.setLayoutData(new GridData(GridData.FILL_HORIZONTAL));

		toolkit.createLabel(container,
				Messages.getString("IdAttsSection.id.label")); //$NON-NLS-1$
		idText = toolkit.createText(container,
				Messages.getString("IdAttsSection.id.default")); //$NON-NLS-1$
		idText.setLayoutData(new GridData(GridData.FILL_HORIZONTAL));
		idText.addModifyListener(this);

		toolkit.createLabel(container,
				Messages.getString("IdAttsSection.conref.label")); //$NON-NLS-1$
		conrefText = new FileChooser(container, baseLocation, toolkit);
		conrefText.getControl().setLayoutData(
				new GridData(GridData.FILL_HORIZONTAL));
		conrefText.addModifyListener(this);

		return container;
	}

	protected void load(Element model) {
		ModelUtils.loadText(model, idText, "id"); //$NON-NLS-1$
		ModelUtils.loadFile(model, conrefText, "conref"); //$NON-NLS-1$
	}

	protected void save(Element model) {
		ModelUtils.saveText(model, idText, "id"); //$NON-NLS-1$
		ModelUtils.saveFile(model, conrefText, "conref"); //$NON-NLS-1$
	}

}
