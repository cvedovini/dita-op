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
package org.dita_op.editor.internal.ui.editors.model;

import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;

import javax.xml.parsers.ParserConfigurationException;
import javax.xml.parsers.SAXParser;
import javax.xml.parsers.SAXParserFactory;

import org.apache.xml.serialize.Method;
import org.apache.xml.serialize.OutputFormat;
import org.apache.xml.serialize.XMLSerializer;
import org.dita_op.editor.internal.utils.ExtensibleEntityResolver;
import org.xml.sax.InputSource;
import org.xml.sax.SAXException;
import org.xml.sax.XMLReader;

@SuppressWarnings("restriction")//$NON-NLS-1$
public class ProfileModel {

	private Val val = new Val();

	private ProfileModel() {
	}

	/**
	 * @return the val
	 */
	public Val getVal() {
		return val;
	}

	public static ProfileModel newModel() {
		return new ProfileModel();
	}

	public static ProfileModel loadModel(InputStream in) throws IOException {
		ProfileModel model = new ProfileModel();

		SAXParserFactory factory = SAXParserFactory.newInstance();
		factory.setValidating(false);
		factory.setNamespaceAware(false);

		try {
			SAXParser parser = factory.newSAXParser();
			XMLReader reader = parser.getXMLReader();

			reader.setContentHandler(new ProfileHandler(model));
			reader.setEntityResolver(new ExtensibleEntityResolver());

			reader.parse(new InputSource(in));
		} catch (ParserConfigurationException e) {
			throw (IOException) new IOException().initCause(e);
		} catch (SAXException e) {
			throw (IOException) new IOException().initCause(e);
		}

		return model;
	}

	public static void saveModel(ProfileModel model, OutputStream out)
			throws IOException {
		XMLSerializer serializer = new XMLSerializer();

		try {
			serializer.setOutputFormat(new OutputFormat(Method.XML, "UTF-8", //$NON-NLS-1$
					true));
			serializer.setOutputByteStream(out);

			new ProfileSerializer(serializer).serialized(model);
		} catch (SAXException e) {
			throw (IOException) new IOException().initCause(e);
		}
	}
}
