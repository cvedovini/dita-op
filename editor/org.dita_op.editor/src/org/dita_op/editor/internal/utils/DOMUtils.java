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
package org.dita_op.editor.internal.utils;

import java.io.IOException;
import java.io.StringWriter;
import java.net.URI;

import org.apache.xml.serialize.TextSerializer;
import org.dita_op.editor.internal.Activator;
import org.dita_op.editor.internal.Utils;
import org.eclipse.core.resources.IFile;
import org.eclipse.core.resources.IResource;
import org.eclipse.core.resources.IWorkspaceRoot;
import org.eclipse.core.resources.ResourcesPlugin;
import org.eclipse.core.runtime.IStatus;
import org.eclipse.core.runtime.Path;
import org.eclipse.ui.IWorkbenchPage;
import org.eclipse.ui.PartInitException;
import org.eclipse.ui.PlatformUI;
import org.eclipse.ui.ide.IDE;
import org.w3c.dom.Element;

public class DOMUtils {

	public static String toString(Element elt) {
		StringWriter out = new StringWriter();
		TextSerializer serializer = new TextSerializer();
		serializer.setOutputCharStream(out);

		try {
			serializer.serialize(elt);
		} catch (IOException e) {
			Activator.getDefault().log(IStatus.WARNING, e);
		}

		return out.toString();
	}

	public static String getReference(Element elt) {
		String href = elt.getAttribute("href"); //$NON-NLS-1$

		if (href == null) {
			href = elt.getAttribute("mapref"); //$NON-NLS-1$
		}

		return href;
	}

	public static void open(URI baseLocation, Element elt) {
		String ref = getReference(elt);

		if (ref != null) {
			IFile target = getTargetFile(baseLocation, ref);

			if (target != null && target.exists()) {
				try {
					IWorkbenchPage page = PlatformUI.getWorkbench().getActiveWorkbenchWindow().getActivePage();
					IDE.openEditor(page, target, true);
				} catch (PartInitException e) {
					Activator.getDefault().log(IStatus.WARNING, e);
				}
			}
		}
	}

	public static IFile getTargetFile(URI baseLocation, String ref) {
		URI targetURI = URI.create(ref);

		if (baseLocation != null && !targetURI.isAbsolute()) {
			targetURI = baseLocation.resolve(targetURI);

			IWorkspaceRoot root = ResourcesPlugin.getWorkspace().getRoot();
			return root.getFile(new Path(targetURI.toString()));
		}

		return null;
	}

	public static String getRelativePath(URI baseLocation, IResource res) {
		URI targetURI = URI.create(res.getFullPath().toString());

		if (baseLocation != null) {
			targetURI = Utils.relativize(targetURI, baseLocation);
		}

		return targetURI.toString();
	}

}
