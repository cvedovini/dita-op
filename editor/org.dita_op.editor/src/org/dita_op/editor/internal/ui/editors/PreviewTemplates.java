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
package org.dita_op.editor.internal.ui.editors;

import java.io.IOException;
import java.net.URL;
import java.util.HashMap;
import java.util.Map;

import javax.xml.transform.Templates;
import javax.xml.transform.TransformerConfigurationException;
import javax.xml.transform.TransformerFactory;
import javax.xml.transform.stream.StreamSource;

import org.dita_op.editor.internal.Activator;
import org.eclipse.core.runtime.FileLocator;
import org.eclipse.core.runtime.IProgressMonitor;
import org.eclipse.core.runtime.IStatus;
import org.eclipse.core.runtime.Status;
import org.eclipse.core.runtime.jobs.Job;
import org.osgi.framework.Bundle;

public class PreviewTemplates {

	public static String TOPIC_PREVIEW_TEMPLATE = "/xsl/dita2xhtml.xsl"; //$NON-NLS-1$

	private final Map<String, Templates> templatesCache = new HashMap<String, Templates>(
			2);

	public PreviewTemplates() {
		new LoadTemplateJob(TOPIC_PREVIEW_TEMPLATE).schedule();
	}

	public Templates getTemplates(String templateId) {
		synchronized (templateId) {
			synchronized (templatesCache) {
				return templatesCache.get(templateId);
			}
		}
	}

	private class LoadTemplateJob extends Job {

		private final String templateId;

		private LoadTemplateJob(String templateId) {
			super(Messages.getString("LoadTemplatesJob.title")); //$NON-NLS-1$
			this.templateId = templateId;
			setPriority(Job.LONG);
			setSystem(true);
		}

		@Override
		protected IStatus run(IProgressMonitor monitor) {
			synchronized (templateId) {
				monitor.beginTask(getName(), 3);

				try {
					Bundle bundle = Activator.getDefault().getBundle();
					URL stylesheet = bundle.getEntry(TOPIC_PREVIEW_TEMPLATE);
					stylesheet = FileLocator.resolve(stylesheet);
					monitor.worked(1);

					TransformerFactory factory = TransformerFactory.newInstance();
					monitor.worked(1);

					Templates template = factory.newTemplates(new StreamSource(
							stylesheet.toString()));

					synchronized (templatesCache) {
						templatesCache.put(templateId, template);
					}

					monitor.worked(1);
				} catch (TransformerConfigurationException e) {
					return Activator.getDefault().newStatus(IStatus.ERROR, e);
				} catch (IOException e) {
					return Activator.getDefault().newStatus(IStatus.ERROR, e);
				} finally {
					monitor.done();
				}
			}

			return Status.OK_STATUS;
		}
	}

}
