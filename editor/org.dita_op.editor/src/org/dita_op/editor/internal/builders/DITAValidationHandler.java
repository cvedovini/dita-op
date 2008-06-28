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
package org.dita_op.editor.internal.builders;

import java.io.File;
import java.io.IOException;
import java.net.URISyntaxException;
import java.net.URL;

import org.dita_op.editor.internal.Activator;
import org.dita_op.editor.internal.utils.ExtensibleEntityResolver;
import org.eclipse.core.resources.IMarker;
import org.eclipse.core.runtime.IStatus;
import org.xml.sax.Attributes;
import org.xml.sax.InputSource;
import org.xml.sax.Locator;
import org.xml.sax.SAXException;
import org.xml.sax.SAXParseException;
import org.xml.sax.ext.DefaultHandler2;

public class DITAValidationHandler extends DefaultHandler2 {

	private Locator locator;
	private final ValidationListener listener;
	private final ExtensibleEntityResolver resolver;

	public DITAValidationHandler(ValidationListener listener) {
		this.listener = listener;
		resolver = new ExtensibleEntityResolver();
	}

	@Override
	public void setDocumentLocator(Locator locator) {
		this.locator = locator;
	}

	/**
	 * @see org.xml.sax.helpers.DefaultHandler#resolveEntity(java.lang.String,
	 *      java.lang.String)
	 */
	@Override
	public InputSource resolveEntity(String name, String publicId,
			String baseURI, String systemId) throws IOException, SAXException {
		return resolver.resolveEntity(name, publicId, baseURI, systemId);
	}

	@Override
	public void startElement(String uri, String localname, String qName,
			Attributes attributes) throws SAXException {
		super.startElement(uri, localname, qName, attributes);
		String href = attributes.getValue("href"); //$NON-NLS-1$
		String conref = attributes.getValue("conref"); //$NON-NLS-1$

		if (href != null) {
			// Checks only references for which the scope is local because the
			// target may not be available at the time of the validation for any
			// other type of scope.
			String scope = attributes.getValue("scope"); //$NON-NLS-1$

			if (scope == null || scope.equals("local")) { //$NON-NLS-1$
				validateReference(href);
			}
		}

		if (conref != null) {
			validateReference(conref);
		}

	}

	private void validateReference(String href) throws SAXException {
		try {
			validateReference2(href);
		} catch (IOException e) {
			onError(Messages.getString("DITAValidationHandler.invalidURL", href)); //$NON-NLS-1$
		} catch (URISyntaxException e) {
			onError(Messages.getString("DITAValidationHandler.invalidURL", href)); //$NON-NLS-1$
		}
	}

	private void validateReference2(String href) throws IOException,
			SAXException, URISyntaxException {
		InputSource target = resolveEntity("", null, //$NON-NLS-1$
				locator.getSystemId(), href);

		if (target != null) {
			// TODO: Should validate presence of ID when there
			// is a fragment in the URL
			URL targetUrl = new URL(target.getSystemId());

			if ("file".equals(targetUrl.getProtocol())) { //$NON-NLS-1$
				File targetFile = new File(targetUrl.toURI().getPath());

				if (!targetFile.exists()) {
					Activator.getDefault().log(
							IStatus.ERROR,
							Messages.getString(
									"DITAValidationHandler.couldNotFindTarget", //$NON-NLS-1$
									targetFile.toString()));
					onError(Messages.getString(
							"DITAValidationHandler.couldNotFindTarget", //$NON-NLS-1$
							href));
				}
			} else {
				onWarning(Messages.getString(
						"DITAValidationHandler.targetNotLocal", //$NON-NLS-1$
						href));
			}
		} else {
			onError(Messages.getString(
					"DITAValidationHandler.couldNotFindTarget", //$NON-NLS-1$
					href));
		}
	}

	@Override
	public void error(SAXParseException e) throws SAXException {
		onMessage(IMarker.SEVERITY_ERROR, e.getMessage(), e.getLineNumber());
	}

	@Override
	public void fatalError(SAXParseException e) throws SAXException {
		onMessage(IMarker.SEVERITY_ERROR, e.getMessage(), e.getLineNumber());
	}

	@Override
	public void warning(SAXParseException e) throws SAXException {
		onMessage(IMarker.SEVERITY_WARNING, e.getMessage(), e.getLineNumber());
	}

	private void onError(String message) {
		onMessage(IMarker.SEVERITY_ERROR, message, locator.getLineNumber());
	}

	private void onWarning(String message) {
		onMessage(IMarker.SEVERITY_WARNING, message, locator.getLineNumber());
	}

	private void onMessage(int severity, String message, int lineNumber) {
		listener.onMessage(severity, message, lineNumber);
	}
}