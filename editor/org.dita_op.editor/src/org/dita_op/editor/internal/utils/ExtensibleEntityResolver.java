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
package org.dita_op.editor.internal.utils;

import java.io.IOException;
import java.net.URL;

import org.eclipse.core.runtime.FileLocator;
import org.eclipse.core.runtime.Platform;
import org.eclipse.wst.common.uriresolver.internal.ExtensibleURIResolver;
import org.eclipse.wst.common.uriresolver.internal.provisional.URIResolver;
import org.osgi.framework.Bundle;
import org.xml.sax.InputSource;
import org.xml.sax.SAXException;
import org.xml.sax.ext.EntityResolver2;

public class ExtensibleEntityResolver implements EntityResolver2 {

	private final URIResolver resolver;

	public ExtensibleEntityResolver() {
		this.resolver = new ExtensibleURIResolver();
	}

	/**
	 * @see org.xml.sax.EntityResolver#resolveEntity(java.lang.String,
	 *      java.lang.String)
	 */
	public InputSource resolveEntity(String publicId, String systemId)
			throws SAXException, IOException {
		return null;
	}

	public InputSource getExternalSubset(String name, String baseURI)
			throws SAXException, IOException {
		return null;
	}

	public InputSource resolveEntity(String name, String publicId,
			String baseURI, String systemId) throws SAXException, IOException {
		String source = null;

		// Hack to resolve the ditaval.dtd
		if ("[dtd]".equals(name) && publicId == null //$NON-NLS-1$
				&& "ditaval.dtd".equals(systemId)) { //$NON-NLS-1$
			Bundle bundle = Platform.getBundle("org.dita_op.dita.dtd"); //$NON-NLS-1$

			if (bundle != null) {
				URL location = bundle.getEntry("/dtd/ditaval.dtd"); //$NON-NLS-1$
				location = FileLocator.resolve(location);
				source = location.toExternalForm();
			}
		}

		if (source == null) {
			source = resolver.resolve(baseURI, publicId, systemId);
		}

		if (source != null) {
			InputSource src = new InputSource(source);
			src.setPublicId(publicId);
			return src;
		}

		return null;
	}

}
