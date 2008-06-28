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
package org.dita_op.dita.dtd.internal.ui.preferences;

import org.dita_op.dita.dtd.internal.Activator;
import org.dita_op.dita.dtd.internal.catalog.DITACatalog;
import org.eclipse.core.runtime.preferences.AbstractPreferenceInitializer;
import org.eclipse.jface.preference.IPreferenceStore;

public class DITACatalogPreferenceInitializer extends
		AbstractPreferenceInitializer {

	public DITACatalogPreferenceInitializer() {
	}

	/**
	 * @see org.eclipse.core.runtime.preferences.AbstractPreferenceInitializer#initializeDefaultPreferences()
	 */
	@Override
	public void initializeDefaultPreferences() {
		IPreferenceStore store = Activator.getDefault().getPreferenceStore();
		store.setDefault(DITACatalog.DITA_CATALOG_ID,
				"org.dita_op.dita.dtd11"); //$NON-NLS-1$
	}
}
