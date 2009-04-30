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
import org.eclipse.ui.forms.widgets.FormToolkit;
import org.w3c.dom.Element;

class TopicrefAttsSection extends AbstractAttsSection {

	private Combo collectionTypeCombo;
	private Combo targetTypeCombo;
	private Combo targetScopeCombo;
	private Combo lockTitleCombo;
	private Combo targetFormatCombo;
	private Combo targetLinkingCombo;
	private Combo targetSearchCombo;
	private Combo tocCombo;
	private Combo printCombo;
	private Text chunkText;

	public TopicrefAttsSection(Composite parent, AbstractDetailsPage form) {
		super(parent, form);
		getSection().setText(Messages.getString("TopicrefAttsSection.title")); //$NON-NLS-1$
	}

	protected Composite createClient(Composite parent, FormToolkit toolkit) {
		Composite container = toolkit.createComposite(parent);
		container.setLayout(new GridLayout(2, false));
		container.setLayoutData(new GridData(GridData.FILL_HORIZONTAL));

		toolkit.createLabel(container,
				Messages.getString("TopicrefAttsSection.collectionType.label")); //$NON-NLS-1$
		collectionTypeCombo = new Combo(container, SWT.DROP_DOWN
				| SWT.READ_ONLY);
		toolkit.adapt(collectionTypeCombo, true, true);
		collectionTypeCombo.add(ModelUtils.UNSPECIFIED);
		collectionTypeCombo.add("unordered"); //$NON-NLS-1$
		collectionTypeCombo.add("sequence"); //$NON-NLS-1$
		collectionTypeCombo.add("choice"); //$NON-NLS-1$
		collectionTypeCombo.add("family"); //$NON-NLS-1$
		collectionTypeCombo.add(ModelUtils.USE_CONREF_TARGET);
		collectionTypeCombo.setLayoutData(new GridData(GridData.FILL_HORIZONTAL));
		collectionTypeCombo.addSelectionListener(this);

		toolkit.createLabel(container,
				Messages.getString("TopicrefAttsSection.type.label")); //$NON-NLS-1$
		targetTypeCombo = new Combo(container, SWT.DROP_DOWN);
		toolkit.adapt(targetTypeCombo, true, true);
		targetTypeCombo.add("topic"); //$NON-NLS-1$
		targetTypeCombo.add("concept"); //$NON-NLS-1$
		targetTypeCombo.add("task"); //$NON-NLS-1$
		targetTypeCombo.add("reference"); //$NON-NLS-1$
		targetTypeCombo.add("fig"); //$NON-NLS-1$
		targetTypeCombo.add("table"); //$NON-NLS-1$
		targetTypeCombo.add("li"); //$NON-NLS-1$
		targetTypeCombo.add("fn"); //$NON-NLS-1$
		targetTypeCombo.add("section"); //$NON-NLS-1$
		targetTypeCombo.setLayoutData(new GridData(GridData.FILL_HORIZONTAL));
		targetTypeCombo.addSelectionListener(this);

		toolkit.createLabel(container,
				Messages.getString("TopicrefAttsSection.scope.label")); //$NON-NLS-1$
		targetScopeCombo = new Combo(container, SWT.DROP_DOWN | SWT.READ_ONLY);
		toolkit.adapt(targetScopeCombo, true, true);
		targetScopeCombo.add(ModelUtils.UNSPECIFIED);
		targetScopeCombo.add("local"); //$NON-NLS-1$
		targetScopeCombo.add("peer"); //$NON-NLS-1$
		targetScopeCombo.add("external"); //$NON-NLS-1$
		targetScopeCombo.add(ModelUtils.USE_CONREF_TARGET);
		targetScopeCombo.setLayoutData(new GridData(GridData.FILL_HORIZONTAL));
		targetScopeCombo.addSelectionListener(this);

		toolkit.createLabel(container,
				Messages.getString("TopicrefAttsSection.locktitle.label")); //$NON-NLS-1$
		lockTitleCombo = new Combo(container, SWT.DROP_DOWN | SWT.READ_ONLY);
		toolkit.adapt(lockTitleCombo, true, true);
		lockTitleCombo.add(ModelUtils.UNSPECIFIED);
		lockTitleCombo.add("yes"); //$NON-NLS-1$
		lockTitleCombo.add("no"); //$NON-NLS-1$
		lockTitleCombo.add(ModelUtils.USE_CONREF_TARGET);
		lockTitleCombo.setLayoutData(new GridData(GridData.FILL_HORIZONTAL));
		lockTitleCombo.addSelectionListener(this);

		toolkit.createLabel(container,
				Messages.getString("TopicrefAttsSection.format.label")); //$NON-NLS-1$
		targetFormatCombo = new Combo(container, SWT.DROP_DOWN);
		toolkit.adapt(targetFormatCombo, true, true);
		targetFormatCombo.add("dita"); //$NON-NLS-1$
		targetFormatCombo.add(Messages.getString("TopicrefAttsSection.25")); //$NON-NLS-1$
		targetFormatCombo.add("pdf"); //$NON-NLS-1$
		targetFormatCombo.add("ditamap"); //$NON-NLS-1$
		targetFormatCombo.setLayoutData(new GridData(GridData.FILL_HORIZONTAL));
		targetFormatCombo.addSelectionListener(this);

		toolkit.createLabel(container,
				Messages.getString("TopicrefAttsSection.linking.label")); //$NON-NLS-1$
		targetLinkingCombo = new Combo(container, SWT.DROP_DOWN | SWT.READ_ONLY);
		toolkit.adapt(targetLinkingCombo, true, true);
		targetLinkingCombo.add(ModelUtils.UNSPECIFIED);
		targetLinkingCombo.add("targetonly"); //$NON-NLS-1$
		targetLinkingCombo.add("sourceonly"); //$NON-NLS-1$
		targetLinkingCombo.add("normal"); //$NON-NLS-1$
		targetLinkingCombo.add("none"); //$NON-NLS-1$
		targetLinkingCombo.add(ModelUtils.USE_CONREF_TARGET);
		targetLinkingCombo.setLayoutData(new GridData(GridData.FILL_HORIZONTAL));
		targetLinkingCombo.addSelectionListener(this);

		toolkit.createLabel(container,
				Messages.getString("TopicrefAttsSection.toc.label")); //$NON-NLS-1$
		tocCombo = new Combo(container, SWT.DROP_DOWN | SWT.READ_ONLY);
		toolkit.adapt(tocCombo, true, true);
		tocCombo.add(ModelUtils.UNSPECIFIED);
		tocCombo.add("yes"); //$NON-NLS-1$
		tocCombo.add("no"); //$NON-NLS-1$
		tocCombo.add(ModelUtils.USE_CONREF_TARGET);
		tocCombo.setLayoutData(new GridData(GridData.FILL_HORIZONTAL));
		tocCombo.addSelectionListener(this);

		toolkit.createLabel(container,
				Messages.getString("TopicrefAttsSection.print.label")); //$NON-NLS-1$
		printCombo = new Combo(container, SWT.DROP_DOWN | SWT.READ_ONLY);
		toolkit.adapt(printCombo, true, true);
		printCombo.add(ModelUtils.UNSPECIFIED);
		printCombo.add("yes"); //$NON-NLS-1$
		printCombo.add("no"); //$NON-NLS-1$
		printCombo.add("printonly"); //$NON-NLS-1$
		printCombo.add(ModelUtils.USE_CONREF_TARGET);
		printCombo.setLayoutData(new GridData(GridData.FILL_HORIZONTAL));
		printCombo.addSelectionListener(this);

		toolkit.createLabel(container,
				Messages.getString("TopicrefAttsSection.search.label")); //$NON-NLS-1$
		targetSearchCombo = new Combo(container, SWT.DROP_DOWN | SWT.READ_ONLY);
		toolkit.adapt(targetSearchCombo, true, true);
		targetSearchCombo.add(ModelUtils.UNSPECIFIED);
		targetSearchCombo.add("yes"); //$NON-NLS-1$
		targetSearchCombo.add("no"); //$NON-NLS-1$
		targetSearchCombo.add(ModelUtils.USE_CONREF_TARGET);
		targetSearchCombo.setLayoutData(new GridData(GridData.FILL_HORIZONTAL));
		targetSearchCombo.addSelectionListener(this);

		toolkit.createLabel(container,
				Messages.getString("TopicrefAttsSection.chunk.label")); //$NON-NLS-1$
		chunkText = toolkit.createText(container,
				Messages.getString("TopicrefAttsSection.chunk.default")); //$NON-NLS-1$
		chunkText.setLayoutData(new GridData(GridData.FILL_HORIZONTAL));
		chunkText.addModifyListener(this);

		return container;
	}

	protected void load(Element model) {
		ModelUtils.loadCombo(model, collectionTypeCombo, "collection-type"); //$NON-NLS-1$
		ModelUtils.loadCombo(model, targetTypeCombo, "type"); //$NON-NLS-1$
		ModelUtils.loadCombo(model, targetScopeCombo, "scope"); //$NON-NLS-1$
		ModelUtils.loadCombo(model, lockTitleCombo, "locktitle"); //$NON-NLS-1$
		ModelUtils.loadCombo(model, targetFormatCombo, "format"); //$NON-NLS-1$
		ModelUtils.loadCombo(model, targetLinkingCombo, "linking"); //$NON-NLS-1$
		ModelUtils.loadCombo(model, tocCombo, "toc"); //$NON-NLS-1$
		ModelUtils.loadCombo(model, targetSearchCombo, "search"); //$NON-NLS-1$
		ModelUtils.loadCombo(model, printCombo, "print"); //$NON-NLS-1$
		ModelUtils.loadText(model, chunkText, "chunk"); //$NON-NLS-1$
	}

	protected void save(Element model) {
		ModelUtils.saveCombo(model, collectionTypeCombo, "collection-type"); //$NON-NLS-1$
		ModelUtils.saveCombo(model, targetTypeCombo, "type"); //$NON-NLS-1$
		ModelUtils.saveCombo(model, targetScopeCombo, "scope"); //$NON-NLS-1$
		ModelUtils.saveCombo(model, lockTitleCombo, "locktitle"); //$NON-NLS-1$
		ModelUtils.saveCombo(model, targetFormatCombo, "format"); //$NON-NLS-1$
		ModelUtils.saveCombo(model, targetLinkingCombo, "linking"); //$NON-NLS-1$
		ModelUtils.saveCombo(model, tocCombo, "toc"); //$NON-NLS-1$
		ModelUtils.saveCombo(model, targetSearchCombo, "search"); //$NON-NLS-1$
		ModelUtils.saveCombo(model, printCombo, "print"); //$NON-NLS-1$
		ModelUtils.saveText(model, chunkText, "chunk"); //$NON-NLS-1$
	}

}
