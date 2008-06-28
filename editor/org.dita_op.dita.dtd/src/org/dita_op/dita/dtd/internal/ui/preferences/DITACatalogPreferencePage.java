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
package org.dita_op.dita.dtd.internal.ui.preferences;

import org.dita_op.dita.dtd.internal.Activator;
import org.dita_op.dita.dtd.internal.catalog.DITACatalog;
import org.eclipse.core.runtime.IConfigurationElement;
import org.eclipse.core.runtime.IExtensionRegistry;
import org.eclipse.core.runtime.Platform;
import org.eclipse.jface.preference.ComboFieldEditor;
import org.eclipse.jface.preference.FieldEditorPreferencePage;
import org.eclipse.ui.IWorkbench;
import org.eclipse.ui.IWorkbenchPreferencePage;

public class DITACatalogPreferencePage extends FieldEditorPreferencePage
		implements IWorkbenchPreferencePage {

	public DITACatalogPreferencePage() {
		super(GRID);
		setPreferenceStore(Activator.getDefault().getPreferenceStore());
	}

	public void createFieldEditors() {
		IExtensionRegistry reg = Platform.getExtensionRegistry();
		IConfigurationElement[] extensions = reg.getConfigurationElementsFor(DITACatalog.CATALOG_EXTENSION_ID);
		String[][] catalogs = new String[extensions.length][2];

		for (int i = 0; i < extensions.length; i++) {
			IConfigurationElement element = extensions[i];
			catalogs[i][0] = element.getAttribute("name"); //$NON-NLS-1$
			catalogs[i][1] = element.getAttribute("id"); //$NON-NLS-1$
		}

		addField(new ComboFieldEditor(
				DITACatalog.DITA_CATALOG_ID,
				Messages.getString("DITACatalogPreferencePage.catalog.label"), catalogs, //$NON-NLS-1$
				getFieldEditorParent()));
	}

	public void init(IWorkbench workbench) {
	}

}