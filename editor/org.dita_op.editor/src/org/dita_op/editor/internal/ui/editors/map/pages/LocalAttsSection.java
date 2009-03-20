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
package org.dita_op.editor.internal.ui.editors.map.pages;

import org.eclipse.swt.SWT;
import org.eclipse.swt.layout.GridData;
import org.eclipse.swt.layout.GridLayout;
import org.eclipse.swt.widgets.Combo;
import org.eclipse.swt.widgets.Composite;
import org.eclipse.swt.widgets.Text;
import org.eclipse.ui.forms.AbstractFormPart;
import org.eclipse.ui.forms.widgets.FormToolkit;
import org.w3c.dom.Element;

class LocalAttsSection extends AbstractAttsSection {

	private Combo translateCombo;
	private Text langText;
	private Combo dirCombo;

	public LocalAttsSection(Composite parent, AbstractFormPart form) {
		super(parent, form);
		getSection().setText(Messages.getString("LocalAttsSection.title")); //$NON-NLS-1$
	}

	protected Composite createClient(Composite parent, FormToolkit toolkit) {
		Composite container = toolkit.createComposite(parent);
		container.setLayout(new GridLayout(2, false));
		container.setLayoutData(new GridData(GridData.FILL_HORIZONTAL));

		toolkit.createLabel(container,
				Messages.getString("LocalAttsSection.translate.label")); //$NON-NLS-1$
		translateCombo = new Combo(container, SWT.DROP_DOWN | SWT.READ_ONLY);
		toolkit.adapt(translateCombo, true, true);
		translateCombo.add(ModelUtils.UNSPECIFIED);
		translateCombo.add("yes"); //$NON-NLS-1$
		translateCombo.add("no"); //$NON-NLS-1$
		translateCombo.add(ModelUtils.USE_CONREF_TARGET);
		translateCombo.setLayoutData(new GridData(GridData.FILL_HORIZONTAL));
		translateCombo.addSelectionListener(this);

		toolkit.createLabel(container,
				Messages.getString("LocalAttsSection.lang.label")); //$NON-NLS-1$
		langText = toolkit.createText(container,
				Messages.getString("LocalAttsSection.lang.defaut")); //$NON-NLS-1$
		langText.setLayoutData(new GridData(GridData.FILL_HORIZONTAL));
		langText.addModifyListener(this);

		toolkit.createLabel(container,
				Messages.getString("LocalAttsSection.dir.label")); //$NON-NLS-1$
		dirCombo = new Combo(container, SWT.DROP_DOWN | SWT.READ_ONLY);
		toolkit.adapt(dirCombo, true, true);
		dirCombo.add(ModelUtils.UNSPECIFIED);
		dirCombo.add("ltr"); //$NON-NLS-1$
		dirCombo.add("rtl"); //$NON-NLS-1$
		dirCombo.add("lro"); //$NON-NLS-1$
		dirCombo.add("rlo"); //$NON-NLS-1$
		dirCombo.add(ModelUtils.USE_CONREF_TARGET);
		dirCombo.setLayoutData(new GridData(GridData.FILL_HORIZONTAL));
		dirCombo.addSelectionListener(this);

		return container;
	}

	protected void load(Element model) {
		ModelUtils.loadCombo(model, translateCombo, "translate"); //$NON-NLS-1$
		ModelUtils.loadText(model, langText, "xml:lang"); //$NON-NLS-1$
		ModelUtils.loadCombo(model, dirCombo, "dir"); //$NON-NLS-1$
	}

	protected void save(Element model) {
		ModelUtils.saveCombo(model, translateCombo, "translate"); //$NON-NLS-1$
		ModelUtils.saveText(model, langText, "xml:lang"); //$NON-NLS-1$
		ModelUtils.saveCombo(model, dirCombo, "dir"); //$NON-NLS-1$
	}

}
