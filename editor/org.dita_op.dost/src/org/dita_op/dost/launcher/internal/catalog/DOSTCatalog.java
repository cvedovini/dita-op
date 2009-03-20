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
package org.dita_op.dost.launcher.internal.catalog;

import java.io.File;
import java.io.IOException;

import org.apache.xerces.util.XMLCatalogResolver;
import org.dita_op.dost.launcher.internal.Activator;
import org.dita_op.dost.launcher.internal.DOSTParameters;
import org.eclipse.core.runtime.IStatus;
import org.eclipse.core.runtime.Status;
import org.eclipse.jface.preference.IPreferenceStore;
import org.eclipse.jface.util.IPropertyChangeListener;
import org.eclipse.jface.util.PropertyChangeEvent;
import org.xml.sax.InputSource;
import org.xml.sax.SAXException;
import org.xml.sax.ext.EntityResolver2;

public class DOSTCatalog implements EntityResolver2 {

	private EntityResolver2 _resolver;
	private final IPreferenceStore store;
	private final IPropertyChangeListener propListener = new IPropertyChangeListener() {

		public void propertyChange(PropertyChangeEvent event) {
			if (DOSTParameters.DITA_DIR.equals(event.getProperty())) {
				_resolver = null;
			}
		}
	};

	public DOSTCatalog() {
		store = Activator.getDefault().getPreferenceStore();
		store.addPropertyChangeListener(propListener);
	}

	/**
	 * @see org.xml.sax.ext.EntityResolver2#getExternalSubset(java.lang.String,
	 *      java.lang.String)
	 */
	public InputSource getExternalSubset(String name, String baseURI)
			throws SAXException, IOException {
		EntityResolver2 delegate = getResolver();
		return (delegate == null) ? null : delegate.getExternalSubset(name,
				baseURI);
	}

	/**
	 * @see org.xml.sax.ext.EntityResolver2#resolveEntity(java.lang.String,
	 *      java.lang.String, java.lang.String, java.lang.String)
	 */
	public InputSource resolveEntity(String name, String publicId,
			String baseURI, String systemId) throws SAXException, IOException {
		EntityResolver2 delegate = getResolver();
		return (delegate == null) ? null : delegate.resolveEntity(name,
				publicId, baseURI, systemId);
	}

	/**
	 * @see org.xml.sax.EntityResolver#resolveEntity(java.lang.String,
	 *      java.lang.String)
	 */
	public InputSource resolveEntity(String publicId, String systemId)
			throws SAXException, IOException {
		EntityResolver2 delegate = getResolver();
		return (delegate == null) ? null : delegate.resolveEntity(publicId,
				systemId);
	}

	private synchronized EntityResolver2 getResolver() {
		if (_resolver == null) {
			_resolver = loadResolver();
		}

		return _resolver;
	}

	private EntityResolver2 loadResolver() {
		String ditadir = store.getString(DOSTParameters.DITA_DIR);
		File catalogFile = new File(new File(ditadir), "catalog-dita.xml"); //$NON-NLS-1$

		if (catalogFile.exists()) {
			String uri = catalogFile.toURI().toString();

			XMLCatalogResolver catalog = new XMLCatalogResolver();
			catalog.setPreferPublic(true);
			catalog.setCatalogList(new String[] { uri.toString() });
			return catalog;
		} else {
			IStatus status = new Status(IStatus.ERROR, Activator.PLUGIN_ID,
					Messages.getString("DOSTCatalog.cannot_find_catalog") + catalogFile); //$NON-NLS-1$
			Activator.getDefault().getLog().log(status);
			return null;
		}
	}

}
