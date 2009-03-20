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

class SelectionAttsSection extends AbstractAttsSection {

	private Text platformText;
	private Text productText;
	private Text audienceText;
	private Text otherPropsText;
	private Combo importanceCombo;
	private Text revText;
	private Combo statusCombo;

	public SelectionAttsSection(Composite parent, AbstractFormPart form) {
		super(parent, form);
		getSection().setText(Messages.getString("SelectionAttsSection.title")); //$NON-NLS-1$
	}

	protected Composite createClient(Composite parent, FormToolkit toolkit) {
		Composite container = toolkit.createComposite(parent);
		container.setLayout(new GridLayout(2, false));
		container.setLayoutData(new GridData(GridData.FILL_HORIZONTAL));

		toolkit.createLabel(container,
				Messages.getString("SelectionAttsSection.platform.label")); //$NON-NLS-1$
		platformText = toolkit.createText(container,
				Messages.getString("SelectionAttsSection.platform.default")); //$NON-NLS-1$
		platformText.setLayoutData(new GridData(GridData.FILL_HORIZONTAL));
		platformText.addModifyListener(this);

		toolkit.createLabel(container,
				Messages.getString("SelectionAttsSection.product.label")); //$NON-NLS-1$
		productText = toolkit.createText(container,
				Messages.getString("SelectionAttsSection.product.default")); //$NON-NLS-1$
		productText.setLayoutData(new GridData(GridData.FILL_HORIZONTAL));
		productText.addModifyListener(this);

		toolkit.createLabel(container,
				Messages.getString("SelectionAttsSection.audience.label")); //$NON-NLS-1$
		audienceText = toolkit.createText(container,
				Messages.getString("SelectionAttsSection.audience.default")); //$NON-NLS-1$
		audienceText.setLayoutData(new GridData(GridData.FILL_HORIZONTAL));
		audienceText.addModifyListener(this);

		toolkit.createLabel(container,
				Messages.getString("SelectionAttsSection.others.label")); //$NON-NLS-1$
		otherPropsText = toolkit.createText(container,
				Messages.getString("SelectionAttsSection.others.default")); //$NON-NLS-1$
		otherPropsText.setLayoutData(new GridData(GridData.FILL_HORIZONTAL));
		otherPropsText.addModifyListener(this);

		toolkit.createLabel(container,
				Messages.getString("SelectionAttsSection.importance.label")); //$NON-NLS-1$
		importanceCombo = new Combo(container, SWT.DROP_DOWN | SWT.READ_ONLY);
		toolkit.adapt(importanceCombo, true, true);
		importanceCombo.add(ModelUtils.UNSPECIFIED);
		importanceCombo.add("obsolete"); //$NON-NLS-1$
		importanceCombo.add("deprecated"); //$NON-NLS-1$
		importanceCombo.add("optional"); //$NON-NLS-1$
		importanceCombo.add("default"); //$NON-NLS-1$
		importanceCombo.add("low"); //$NON-NLS-1$
		importanceCombo.add("normal"); //$NON-NLS-1$
		importanceCombo.add("high"); //$NON-NLS-1$
		importanceCombo.add("recommended"); //$NON-NLS-1$
		importanceCombo.add("required"); //$NON-NLS-1$
		importanceCombo.add("urgent"); //$NON-NLS-1$
		importanceCombo.add(ModelUtils.USE_CONREF_TARGET);
		importanceCombo.setLayoutData(new GridData(GridData.FILL_HORIZONTAL));
		importanceCombo.addSelectionListener(this);

		toolkit.createLabel(container,
				Messages.getString("SelectionAttsSection.revision.label")); //$NON-NLS-1$
		revText = toolkit.createText(container,
				Messages.getString("SelectionAttsSection.revision.default")); //$NON-NLS-1$
		revText.setLayoutData(new GridData(GridData.FILL_HORIZONTAL));
		revText.addModifyListener(this);

		toolkit.createLabel(container,
				Messages.getString("SelectionAttsSection.status.label")); //$NON-NLS-1$
		statusCombo = new Combo(container, SWT.DROP_DOWN | SWT.READ_ONLY);
		toolkit.adapt(statusCombo, true, true);
		statusCombo.add(ModelUtils.UNSPECIFIED);
		statusCombo.add("new"); //$NON-NLS-1$
		statusCombo.add("changed"); //$NON-NLS-1$
		statusCombo.add("deleted"); //$NON-NLS-1$
		statusCombo.add("unchanged"); //$NON-NLS-1$
		statusCombo.add(ModelUtils.USE_CONREF_TARGET);
		statusCombo.setLayoutData(new GridData(GridData.FILL_HORIZONTAL));
		statusCombo.addSelectionListener(this);

		return container;
	}

	protected void load(Element model) {
		ModelUtils.loadText(model, platformText, "platform"); //$NON-NLS-1$
		ModelUtils.loadText(model, productText, "product"); //$NON-NLS-1$
		ModelUtils.loadText(model, audienceText, "audience"); //$NON-NLS-1$
		ModelUtils.loadText(model, otherPropsText, "otherprops"); //$NON-NLS-1$
		ModelUtils.loadCombo(model, importanceCombo, "importance"); //$NON-NLS-1$
		ModelUtils.loadText(model, revText, "rev"); //$NON-NLS-1$
		ModelUtils.loadCombo(model, statusCombo, "status"); //$NON-NLS-1$
	}

	protected void save(Element model) {
		ModelUtils.saveText(model, platformText, "platform"); //$NON-NLS-1$
		ModelUtils.saveText(model, productText, "product"); //$NON-NLS-1$
		ModelUtils.saveText(model, audienceText, "audience"); //$NON-NLS-1$
		ModelUtils.saveText(model, otherPropsText, "otherprops"); //$NON-NLS-1$
		ModelUtils.saveCombo(model, importanceCombo, "importance"); //$NON-NLS-1$
		ModelUtils.saveText(model, revText, "rev"); //$NON-NLS-1$
		ModelUtils.saveCombo(model, statusCombo, "status"); //$NON-NLS-1$
	}

}
