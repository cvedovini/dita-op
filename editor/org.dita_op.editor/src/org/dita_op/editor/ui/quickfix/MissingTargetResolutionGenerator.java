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
package org.dita_op.editor.ui.quickfix;

import java.io.ByteArrayInputStream;
import java.io.InputStream;
import java.util.ArrayList;
import java.util.List;

import org.dita_op.editor.internal.Activator;
import org.dita_op.editor.internal.ImageConstants;
import org.dita_op.editor.internal.ui.templates.DITATemplateContext;
import org.eclipse.core.resources.IFile;
import org.eclipse.core.resources.IMarker;
import org.eclipse.core.resources.IResource;
import org.eclipse.core.runtime.CoreException;
import org.eclipse.core.runtime.IProgressMonitor;
import org.eclipse.core.runtime.IStatus;
import org.eclipse.core.runtime.Path;
import org.eclipse.core.runtime.Status;
import org.eclipse.core.runtime.jobs.Job;
import org.eclipse.jface.text.BadLocationException;
import org.eclipse.jface.text.templates.TemplateException;
import org.eclipse.swt.graphics.Image;
import org.eclipse.ui.IMarkerResolution;
import org.eclipse.ui.IMarkerResolutionGenerator2;
import org.eclipse.ui.views.markers.WorkbenchMarkerResolution;

public class MissingTargetResolutionGenerator implements
		IMarkerResolutionGenerator2 {

	private static final String MISSING_REF_TARGET_MARKER = Activator.PLUGIN_ID
			+ ".missingTarget"; //$NON-NLS-1$

	public MissingTargetResolutionGenerator() {
	}

	public boolean hasResolutions(IMarker marker) {
		try {
			return marker.isSubtypeOf(MISSING_REF_TARGET_MARKER);
		} catch (CoreException e) {
			throw new RuntimeException(e);
		}
	}

	public IMarkerResolution[] getResolutions(IMarker marker) {
		String type = marker.getAttribute("type", "any"); //$NON-NLS-1$ //$NON-NLS-2$

		if ("map".equals(type)) { //$NON-NLS-1$
			return new IMarkerResolution[] { new CreateMapResolution(marker) };
		} else {
			return new IMarkerResolution[] {
					new CreateConceptResolution(marker),
					new CreateTaskResolution(marker),
					new CreateReferenceResolution(marker),
					new CreateTopicResolution(marker),
					new CreateMapResolution(marker),
					new CreateEmptyFileResolution(marker) };
		}
	}

	private abstract static class CreateTargetResolution extends
			WorkbenchMarkerResolution {

		private final String templateId;
		private final IMarker marker;

		public CreateTargetResolution(IMarker marker, String templateId) {
			this.marker = marker;
			this.templateId = templateId;
		}

		@Override
		public IMarker[] findOtherMarkers(IMarker[] markers) {
			List<IMarker> others = new ArrayList<IMarker>(markers.length);

			for (IMarker other : markers) {
				try {
					if (other != marker
							&& other.isSubtypeOf(MISSING_REF_TARGET_MARKER)) {
						others.add(other);
					}
				} catch (CoreException e) {
				}
			}

			return others.toArray(new IMarker[others.size()]);
		}

		public Image getImage() {
			return Activator.getDefault().getImage(
					ImageConstants.ICON_TOPIC_ADD);
		}

		public String getDescription() {
			return null;
		}

		public void run(final IMarker marker) {
			Job job = new Job(getLabel()) {
				public IStatus run(IProgressMonitor monitor) {
					return doRun(marker, monitor);
				}
			};

			job.setUser(true);
			job.schedule();
		}

		private IStatus doRun(IMarker marker, IProgressMonitor monitor) {
			try {
				IResource source = marker.getResource();
				String href = (String) marker.getAttribute("href"); //$NON-NLS-1$
				IFile target = source.getParent().getFile(new Path(href));

				InputStream stream = null;

				if (templateId != null) {
					String contents = DITATemplateContext.evaluateTemplate(templateId);
					stream = new ByteArrayInputStream(contents.getBytes());
				} else {
					stream = new ByteArrayInputStream(new byte[0]);
				}

				target.create(stream, true, monitor);
				marker.delete();

				return Status.OK_STATUS;
			} catch (BadLocationException e) {
				return new Status(IStatus.ERROR, Activator.PLUGIN_ID,
						e.getLocalizedMessage(), e);
			} catch (TemplateException e) {
				return new Status(IStatus.ERROR, Activator.PLUGIN_ID,
						e.getLocalizedMessage(), e);
			} catch (CoreException e) {
				return e.getStatus();
			}
		}

	}

	private static class CreateTopicResolution extends CreateTargetResolution {

		public CreateTopicResolution(IMarker marker) {
			super(marker, "org.dita_op.editor.template.topic"); //$NON-NLS-1$
		}

		public String getLabel() {
			return Messages.getString("MissingTargetResolutionGenerator.topic"); //$NON-NLS-1$
		}

	}

	private static class CreateConceptResolution extends CreateTargetResolution {

		public CreateConceptResolution(IMarker marker) {
			super(marker, "org.dita_op.editor.template.concept"); //$NON-NLS-1$
		}

		public String getLabel() {
			return Messages.getString("MissingTargetResolutionGenerator.concept"); //$NON-NLS-1$
		}

	}

	private static class CreateTaskResolution extends CreateTargetResolution {

		public CreateTaskResolution(IMarker marker) {
			super(marker, "org.dita_op.editor.template.task"); //$NON-NLS-1$
		}

		public String getLabel() {
			return Messages.getString("MissingTargetResolutionGenerator.task"); //$NON-NLS-1$
		}

	}

	private static class CreateReferenceResolution extends
			CreateTargetResolution {

		public CreateReferenceResolution(IMarker marker) {
			super(marker, "org.dita_op.editor.template.reference"); //$NON-NLS-1$
		}

		public String getLabel() {
			return Messages.getString("MissingTargetResolutionGenerator.reference"); //$NON-NLS-1$
		}

	}

	private static class CreateMapResolution extends CreateTargetResolution {

		public CreateMapResolution(IMarker marker) {
			super(marker, "org.dita_op.editor.template.map"); //$NON-NLS-1$
		}

		public String getLabel() {
			return Messages.getString("MissingTargetResolutionGenerator.map"); //$NON-NLS-1$
		}

		public Image getImage() {
			return Activator.getDefault().getImage(
					ImageConstants.ICON_DITAMAP_ADD);
		}

	}

	private static class CreateEmptyFileResolution extends
			CreateTargetResolution {

		public CreateEmptyFileResolution(IMarker marker) {
			super(marker, null);
		}

		public String getLabel() {
			return Messages.getString("MissingTargetResolutionGenerator.empty"); //$NON-NLS-1$
		}

		public Image getImage() {
			return Activator.getDefault().getImage(ImageConstants.ICON_FILE_ADD);
		}

	}

}
