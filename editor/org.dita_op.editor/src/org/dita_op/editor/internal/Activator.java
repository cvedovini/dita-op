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
package org.dita_op.editor.internal;

import org.dita_op.editor.internal.ui.editors.PreviewTemplates;
import org.eclipse.core.runtime.IStatus;
import org.eclipse.core.runtime.Status;
import org.eclipse.jface.resource.ImageDescriptor;
import org.eclipse.swt.graphics.Image;
import org.eclipse.swt.widgets.Display;
import org.eclipse.ui.forms.FormColors;
import org.eclipse.ui.plugin.AbstractUIPlugin;
import org.osgi.framework.BundleContext;

/**
 * The activator class controls the plug-in life cycle
 */
public class Activator extends AbstractUIPlugin {

	// The plug-in ID
	public static final String PLUGIN_ID = "org.dita_op.editor"; //$NON-NLS-1$

	// The shared instance
	private static Activator plugin;

	private PreviewTemplates previewTemplates;

	private FormColors formColors;

	/**
	 * The constructor
	 */
	public Activator() {
		plugin = this;
	}

	/**
	 * @see org.eclipse.core.runtime.Plugins#start(org.osgi.framework.BundleContext)
	 */
	@Override
	public void start(final BundleContext context) throws Exception {
		super.start(context);
		previewTemplates = new PreviewTemplates();
	}

	/**
	 * @see org.eclipse.core.runtime.Plugin#stop(org.osgi.framework.BundleContext)
	 */
	@Override
	public void stop(BundleContext context) throws Exception {
		if (formColors != null) {
			formColors.dispose();
			formColors = null;
		}
		previewTemplates = null;
		plugin = null;
		super.stop(context);
	}

	public IStatus newStatus(int severity, Exception e) {
		return new Status(severity, PLUGIN_ID, e.getLocalizedMessage(), e);
	}

	public void log(int severity, Exception e) {
		getLog().log(newStatus(severity, e));
	}

	public void log(int severity, String message) {
		getLog().log(new Status(severity, PLUGIN_ID, message, null));
	}

	public PreviewTemplates getPreviewTemplates() {
		return previewTemplates;
	}

	/**
	 * Returns the shared instance
	 * 
	 * @return the shared instance
	 */
	public static Activator getDefault() {
		return plugin;
	}

	public FormColors getFormColors(Display display) {
		if (formColors == null) {
			formColors = new FormColors(display);
			formColors.markShared();
		}
		return formColors;
	}

	public static ImageDescriptor getImageDescriptor(String path) {
		return imageDescriptorFromPlugin(PLUGIN_ID, path);
	}

	public Image getImage(String path) {
		Image image = getImageRegistry().get(path);

		if (image == null) {
			ImageDescriptor descriptor = getImageDescriptor(path);
			getImageRegistry().put(path, descriptor);
			image = getImageRegistry().get(path);
		}

		return image;
	}

}
