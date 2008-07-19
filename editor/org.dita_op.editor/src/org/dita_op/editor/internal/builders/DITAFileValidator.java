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

import java.io.IOException;
import java.text.MessageFormat;
import java.util.Map;

import javax.xml.parsers.ParserConfigurationException;
import javax.xml.parsers.SAXParser;
import javax.xml.parsers.SAXParserFactory;

import org.dita_op.editor.internal.Activator;
import org.eclipse.core.resources.IFile;
import org.eclipse.core.resources.IMarker;
import org.eclipse.core.resources.IProject;
import org.eclipse.core.resources.IResource;
import org.eclipse.core.resources.IResourceDelta;
import org.eclipse.core.resources.IResourceDeltaVisitor;
import org.eclipse.core.resources.IResourceVisitor;
import org.eclipse.core.resources.IncrementalProjectBuilder;
import org.eclipse.core.runtime.CoreException;
import org.eclipse.core.runtime.IProgressMonitor;
import org.eclipse.core.runtime.Platform;
import org.eclipse.core.runtime.content.IContentType;
import org.eclipse.core.runtime.content.IContentTypeManager;
import org.xml.sax.InputSource;
import org.xml.sax.SAXException;
import org.xml.sax.SAXParseException;
import org.xml.sax.XMLReader;

public class DITAFileValidator extends IncrementalProjectBuilder {

	public static final String BUILDER_ID = Activator.PLUGIN_ID
			+ ".DITAFileValidator"; //$NON-NLS-1$

	private static final String MARKER_TYPE = Activator.PLUGIN_ID
			+ ".DITAProblem"; //$NON-NLS-1$

	private SAXParserFactory parserFactory;
	private final IContentType ditaContentType;

	public DITAFileValidator() {
		IContentTypeManager ctm = Platform.getContentTypeManager();
		ditaContentType = ctm.getContentType("org.dita_op.dita"); //$NON-NLS-1$
	}

	/**
	 * @see org.eclipse.core.resources.IncrementalProjectBuilder#build(int,
	 *      java.util.Map, org.eclipse.core.runtime.IProgressMonitor)
	 */
	@Override
	@SuppressWarnings("unchecked")//$NON-NLS-1$
	protected IProject[] build(int kind, Map args, IProgressMonitor monitor)
			throws CoreException {
		if (kind == FULL_BUILD) {
			fullBuild(monitor);
		} else {
			IResourceDelta delta = getDelta(getProject());

			if (delta == null) {
				fullBuild(monitor);
			} else {
				incrementalBuild(delta, monitor);
			}
		}

		return null;
	}

	protected void fullBuild(final IProgressMonitor monitor)
			throws CoreException {
		IProject project = getProject();
		monitor.beginTask(
				MessageFormat.format(
						Messages.getString("DITAFileValidator.projectValidationTaskName"), //$NON-NLS-1$
						project.getName()), IProgressMonitor.UNKNOWN);

		try {
			project.accept(new DITAResourceVisitor(monitor));
		} finally {
			monitor.done();
		}
	}

	protected void incrementalBuild(IResourceDelta delta,
			IProgressMonitor monitor) throws CoreException {
		IProject project = getProject();
		monitor.beginTask(
				MessageFormat.format(
						Messages.getString("DITAFileValidator.projectValidationTaskName"), //$NON-NLS-1$
						project.getName()), IProgressMonitor.UNKNOWN);

		try {
			// the visitor does the work.
			delta.accept(new DITAResourceVisitor(monitor));
		} finally {
			monitor.done();
		}
	}

	protected boolean isDITAFile(IResource resource) {
		if (resource.getType() == IResource.FILE) {
			IContentTypeManager ctm = Platform.getContentTypeManager();
			IContentType[] actualContentTypes = ctm.findContentTypesFor(resource.getName());

			for (IContentType actualContentType : actualContentTypes) {
				if (actualContentType.isKindOf(ditaContentType)) {
					return true;
				}
			}
		}

		return false;
	}

	protected void validate(final IFile file, IProgressMonitor monitor) {
		monitor.subTask(file.getName());

		try {
			String systemId = file.getLocation().toFile().toURL().toString();
			SAXParser parser = getParser();

			XMLReader reader = parser.getXMLReader();

			DITAValidationHandler handler = new DITAValidationHandler(
					new ValidationListener() {

						public void onMessage(int severity, String message,
								int lineNumber) {
							addMarker(file, severity, message, lineNumber);
						}
					});

			reader.setContentHandler(handler);
			reader.setErrorHandler(handler);
			reader.setEntityResolver(handler);

			InputSource source = new InputSource(file.getContents());
			source.setSystemId(systemId);
			reader.parse(source);
		} catch (CoreException e) {
			addMarker(file, IMarker.SEVERITY_ERROR, e.getMessage(), 1);
		} catch (IOException e) {
			addMarker(file, IMarker.SEVERITY_ERROR, e.getMessage(), 1);
		} catch (SAXParseException e) {
			addMarker(file, IMarker.SEVERITY_ERROR, e.getMessage(),
					e.getLineNumber());
		} catch (SAXException e) {
			addMarker(file, IMarker.SEVERITY_ERROR, e.getMessage(), 1);
		} catch (ParserConfigurationException e) {
			addMarker(file, IMarker.SEVERITY_ERROR, e.getMessage(), 1);
		} finally {
			monitor.worked(1);
		}
	}

	void addMarker(IFile file, int severity, String message, int lineNumber) {
		try {
			IMarker marker = file.createMarker(MARKER_TYPE);
			marker.setAttribute(IMarker.MESSAGE, message);
			marker.setAttribute(IMarker.SEVERITY, severity);

			if (lineNumber == -1) {
				lineNumber = 1;
			}

			marker.setAttribute(IMarker.LINE_NUMBER, lineNumber);
		} catch (CoreException e) {
			Activator.getDefault().getLog().log(e.getStatus());
		}
	}

	private void deleteMarkers(IResource resource) {
		try {
			resource.deleteMarkers(MARKER_TYPE, false, IResource.DEPTH_ZERO);
		} catch (CoreException e) {
			Activator.getDefault().getLog().log(e.getStatus());
		}
	}

	private SAXParser getParser() throws ParserConfigurationException,
			SAXException {
		if (parserFactory == null) {
			parserFactory = SAXParserFactory.newInstance();
			parserFactory.setValidating(true);
			parserFactory.setNamespaceAware(false);
			parserFactory.setFeature(
					"http://apache.org/xml/features/validation/dynamic",
					Boolean.TRUE);
			parserFactory.setFeature(
					"http://apache.org/xml/features/validation/schema",
					Boolean.TRUE);
		}

		return parserFactory.newSAXParser();
	}

	private class DITAResourceVisitor implements IResourceDeltaVisitor,
			IResourceVisitor {
		private final IProgressMonitor monitor;

		public DITAResourceVisitor(IProgressMonitor monitor) {
			this.monitor = monitor;
		}

		/**
		 * @see org.eclipse.core.resources.IResourceDeltaVisitor#visit(org.eclipse.core.resources.IResourceDelta)
		 */
		public boolean visit(IResourceDelta delta) throws CoreException {
			IResource resource = delta.getResource();
			deleteMarkers(resource);

			if (isDITAFile(resource)) {
				IFile file = (IFile) resource;

				switch (delta.getKind()) {
				case IResourceDelta.ADDED:
				case IResourceDelta.CHANGED:
					validate(file, monitor);
					break;

				case IResourceDelta.REMOVED:
					// TODO: Must do something when deleting referenced
					// resources
					break;
				}
			}

			// return true to continue visiting children.
			return true;
		}

		public boolean visit(IResource resource) {
			deleteMarkers(resource);

			if (isDITAFile(resource)) {
				IFile file = (IFile) resource;
				validate(file, monitor);
			}

			// return true to continue visiting children.
			return true;
		}
	}

}
