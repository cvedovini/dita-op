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
package org.dita_op.dita.dtd.internal.catalog;

import java.io.IOException;
import java.net.URL;
import java.text.MessageFormat;

import org.apache.xerces.util.XMLCatalogResolver;
import org.dita_op.dita.dtd.internal.Activator;
import org.eclipse.core.resources.IFile;
import org.eclipse.core.runtime.CoreException;
import org.eclipse.core.runtime.FileLocator;
import org.eclipse.core.runtime.IConfigurationElement;
import org.eclipse.core.runtime.IExtensionRegistry;
import org.eclipse.core.runtime.IStatus;
import org.eclipse.core.runtime.Platform;
import org.eclipse.core.runtime.Status;
import org.eclipse.jface.preference.IPreferenceStore;
import org.eclipse.jface.util.IPropertyChangeListener;
import org.eclipse.jface.util.PropertyChangeEvent;
import org.eclipse.wst.common.uriresolver.internal.provisional.URIResolverExtension;
import org.osgi.framework.Bundle;
import org.xml.sax.InputSource;
import org.xml.sax.SAXException;
import org.xml.sax.ext.EntityResolver2;

public class DITACatalog implements URIResolverExtension {

	public static final String CATALOG_EXTENSION_ID = Activator.PLUGIN_ID
			+ ".catalogs"; //$NON-NLS-1$
	public static final String DITA_CATALOG_ID = "__dita_catalog_id"; //$NON-NLS-1$

	private EntityResolver2 _resolver;
	private final IPreferenceStore store;
	private final IPropertyChangeListener propListener = new IPropertyChangeListener() {

		public void propertyChange(PropertyChangeEvent event) {
			if (DITA_CATALOG_ID.equals(event.getProperty())) {
				_resolver = null;
			}
		}
	};

	public DITACatalog() {
		store = Activator.getDefault().getPreferenceStore();
		store.addPropertyChangeListener(propListener);
	}

	public String resolve(IFile file, String baseLocation, String publicId,
			String systemId) {
		try {
			EntityResolver2 resolver = getResolver();

			if (resolver != null) {
				InputSource is = resolver.resolveEntity("[dtd]", publicId, //$NON-NLS-1$
						baseLocation, systemId);

				if (is != null) {
					return is.getSystemId();
				}
			}

			// Hack to resolve the ditaval.dtd when not in the catalog
			if (systemId != null && systemId.endsWith("ditaval.dtd")) { //$NON-NLS-1$
				Bundle bundle = Activator.getDefault().getBundle();
				URL location = bundle.getEntry("/etc/ditaval.dtd"); //$NON-NLS-1$

				try {
					location = FileLocator.resolve(location);
				} catch (IOException e) {
					throw newCoreException(e);
				}

				return location.toExternalForm();
			}
		} catch (CoreException e) {
			log(e, publicId, systemId);
		} catch (SAXException e) {
			log(e, publicId, systemId);
		} catch (IOException e) {
			log(e, publicId, systemId);
		}

		return null;
	}

	private void log(Exception e, String publicId, String systemId) {
		String msg = MessageFormat.format(
				"Cannot resolve entity [{0}] [{1}]", publicId, systemId); //$NON-NLS-1$
		Activator.getDefault().getLog().log(computeStatus(msg, e));
	}

	private synchronized EntityResolver2 getResolver() throws CoreException {
		if (_resolver == null) {
			_resolver = loadResolver();
		}

		return _resolver;
	}

	private EntityResolver2 loadResolver() throws CoreException {
		String version = store.getString(DITACatalog.DITA_CATALOG_ID);

		IExtensionRegistry reg = Platform.getExtensionRegistry();
		IConfigurationElement[] extensions = reg.getConfigurationElementsFor(DITACatalog.CATALOG_EXTENSION_ID);

		for (int i = 0; i < extensions.length; i++) {
			IConfigurationElement element = extensions[i];

			if (version.equals(element.getAttribute("id"))) { //$NON-NLS-1$
				String className = element.getAttribute("class"); //$NON-NLS-1$
				IStatus status = new Status(IStatus.INFO, Activator.PLUGIN_ID,
						Messages.getString("DITACatalog.loading") + element.getAttribute("name")); //$NON-NLS-1$ //$NON-NLS-2$
				Activator.getDefault().getLog().log(status);

				if (className != null) {
					return (EntityResolver2) element.createExecutableExtension("class"); //$NON-NLS-1$
				} else {
					String contributor = extensions[i].getContributor().getName();
					Bundle bundle = Platform.getBundle(contributor);
					URL uri = bundle.getEntry(element.getAttribute("uri")); //$NON-NLS-1$

					try {
						uri = FileLocator.resolve(uri);
					} catch (IOException e) {
						throw newCoreException(e);
					}

					XMLCatalogResolver catalog = new XMLCatalogResolver();
					catalog.setPreferPublic(true);
					catalog.setCatalogList(new String[] { uri.toString() });
					return catalog;
				}
			}
		}

		return null;
	}

	private IStatus computeStatus(String message, Exception e) {
		if (e instanceof CoreException) {
			return ((CoreException) e).getStatus();
		} else {
			return new Status(IStatus.ERROR, Activator.PLUGIN_ID, message, e);
		}
	}

	private CoreException newCoreException(Exception e) {
		return new CoreException(computeStatus(e.getMessage(), e));
	}

}
