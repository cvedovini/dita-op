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
package org.dita_op.editor.internal.ui.editors.map;

import java.io.ByteArrayInputStream;
import java.io.InputStream;
import java.util.Properties;

import org.dita_op.editor.internal.Activator;
import org.dita_op.editor.internal.Utils;
import org.dita_op.editor.internal.ui.templates.DITATemplateContext;
import org.dita_op.editor.internal.utils.DOMUtils;
import org.eclipse.core.resources.IFile;
import org.eclipse.core.runtime.CoreException;
import org.eclipse.core.runtime.IProgressMonitor;
import org.eclipse.core.runtime.IStatus;
import org.eclipse.core.runtime.Status;
import org.eclipse.core.runtime.SubMonitor;
import org.eclipse.core.runtime.SubProgressMonitor;
import org.eclipse.core.runtime.jobs.Job;
import org.eclipse.jface.text.BadLocationException;
import org.eclipse.jface.text.templates.TemplateException;
import org.w3c.dom.Element;
import org.w3c.dom.Node;
import org.w3c.dom.NodeList;

class GenerateStubsJob extends Job {
	private final MasterSection masterSection;
	private final int stubsCount;
	private final Element elt;

	public GenerateStubsJob(MasterSection masterSection, int stubsCount,
			Element elt) {
		super(Messages.getString("GenerateStubsJob.name")); //$NON-NLS-1$
		this.masterSection = masterSection;
		setPriority(Job.INTERACTIVE);
		this.stubsCount = stubsCount;
		this.elt = elt;
	}

	@Override
	protected IStatus run(IProgressMonitor monitor) {
		SubMonitor progress = SubMonitor.convert(monitor, stubsCount);

		try {
			generateStubs(elt, progress.newChild(stubsCount));
			return Status.OK_STATUS;
		} catch (CoreException e) {
			return e.getStatus();
		} finally {
			progress.done();
		}
	}

	private void generateStubs(Element n, IProgressMonitor monitor)
			throws CoreException {
		String localName = n.getLocalName();

		if ("topicref".equals(localName)) { //$NON-NLS-1$
			generateTopicStub(n, monitor);
		} else if ("navref".equals(localName)) { //$NON-NLS-1$
			generateMapStub(n, monitor);
		}

		NodeList children = n.getChildNodes();
		for (int i = 0; i < children.getLength(); i++) {
			Node child = children.item(i);

			if (child instanceof Element) {
				generateStubs((Element) child, monitor);
			}
		}
	}

	private void generateTopicStub(Element n, IProgressMonitor monitor)
			throws CoreException {
		String ref = n.getAttribute("href"); //$NON-NLS-1$

		if (ref == null) {
			ref = computeTopicFilename(n);
			n.setAttribute("href", ref); //$NON-NLS-1$
		}

		String navtitle = n.getAttribute("navtitle"); //$NON-NLS-1$
		IFile target = DOMUtils.getTargetFile(masterSection.getBaseLocation(),
				ref);

		if (!target.exists()) {
			String templateId = "org.dita_op.editor.template.topic"; //$NON-NLS-1$
			String type = n.getAttribute("type"); //$NON-NLS-1$

			if ("concept".equals(type)) { //$NON-NLS-1$
				templateId = "org.dita_op.editor.template.concept"; //$NON-NLS-1$
			} else if ("task".equals(type)) { //$NON-NLS-1$
				templateId = "org.dita_op.editor.template.task"; //$NON-NLS-1$
			} else if ("reference".equals(type)) { //$NON-NLS-1$
				templateId = "org.dita_op.editor.template.reference"; //$NON-NLS-1$
			}

			target.create(openContentStream(templateId, navtitle), true,
					new SubProgressMonitor(monitor, 1));
		}
	}

	private void generateMapStub(Element n, IProgressMonitor monitor)
			throws CoreException {
		String ref = n.getAttribute("mapref"); //$NON-NLS-1$

		if (ref == null) {
			ref = computeMapFilename(n);
			n.setAttribute("mapref", ref); //$NON-NLS-1$
		}

		IFile target = DOMUtils.getTargetFile(masterSection.getBaseLocation(),
				ref);

		if (!target.exists()) {
			target.create(openContentStream("org.dita_op.editor.template.map", //$NON-NLS-1$
					null), true, new SubProgressMonitor(monitor, 1));
		}
	}

	private String computeTopicFilename(Element n) {
		String prefix = null;
		String navtitle = n.getAttribute("navtitle"); //$NON-NLS-1$
		String id = n.getAttribute("id"); //$NON-NLS-1$
		String type = n.getAttribute("type"); //$NON-NLS-1$

		if (navtitle != null) {
			prefix = Utils.slugify(navtitle);
		} else if (id != null) {
			prefix = Utils.slugify(id);
		} else if (type != null) {
			prefix = Utils.slugify(type);
		} else {
			prefix = "topic"; //$NON-NLS-1$
		}

		String filename = prefix.concat(".dita"); //$NON-NLS-1$
		IFile target = DOMUtils.getTargetFile(masterSection.getBaseLocation(),
				filename);

		for (int i = 1; target.exists(); i++) {
			filename = prefix.concat("_").concat(Integer.toString(i)).concat( //$NON-NLS-1$
					".dita"); //$NON-NLS-1$
			target = DOMUtils.getTargetFile(masterSection.getBaseLocation(),
					filename);
		}

		return filename;
	}

	private String computeMapFilename(Element n) {
		String prefix = null;

		String id = n.getAttribute("id"); //$NON-NLS-1$
		if (id != null) {
			prefix = Utils.slugify(id);
		} else {
			prefix = "map"; //$NON-NLS-1$
		}

		String filename = prefix.concat(".ditamap"); //$NON-NLS-1$
		IFile target = DOMUtils.getTargetFile(masterSection.getBaseLocation(),
				filename);

		for (int i = 1; target.exists(); i++) {
			filename = prefix.concat("_").concat(Integer.toString(i)).concat( //$NON-NLS-1$
					".ditamap"); //$NON-NLS-1$
			target = DOMUtils.getTargetFile(masterSection.getBaseLocation(),
					filename);
		}

		return filename;
	}

	private InputStream openContentStream(String templateId, String title)
			throws CoreException {
		Properties vars = null;

		if (title != null) {
			vars = new Properties();
			vars.setProperty("title", title); //$NON-NLS-1$
		}

		try {
			String contents = DITATemplateContext.evaluateTemplate(templateId,
					vars);
			return new ByteArrayInputStream(contents.getBytes());
		} catch (BadLocationException e) {
			throw newCoreException(e);
		} catch (TemplateException e) {
			throw newCoreException(e);
		}
	}

	private CoreException newCoreException(Exception e) throws CoreException {
		IStatus status = Activator.getDefault().newStatus(IStatus.ERROR, e);
		return new CoreException(status);
	}

}