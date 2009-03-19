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
package org.dita_op.editor.internal.ui.editors;

import java.util.ArrayList;
import java.util.List;

import org.dita_op.editor.internal.Activator;
import org.eclipse.core.resources.IFolder;
import org.eclipse.core.resources.IResource;
import org.eclipse.core.resources.IResourceVisitor;
import org.eclipse.core.resources.IWorkspaceRoot;
import org.eclipse.core.resources.ResourcesPlugin;
import org.eclipse.core.runtime.CoreException;
import org.eclipse.core.runtime.IPath;
import org.eclipse.core.runtime.Path;
import org.eclipse.jface.viewers.LabelProvider;
import org.eclipse.ui.model.WorkbenchLabelProvider;
import org.eclipse.wst.sse.core.internal.provisional.text.IStructuredDocumentRegion;
import org.eclipse.wst.sse.core.internal.provisional.text.ITextRegion;
import org.eclipse.wst.sse.core.internal.provisional.text.ITextRegionContainer;
import org.eclipse.wst.sse.core.internal.provisional.text.ITextRegionList;
import org.eclipse.wst.sse.ui.internal.contentassist.CustomCompletionProposal;
import org.eclipse.wst.xml.core.internal.provisional.document.IDOMNode;
import org.eclipse.wst.xml.core.internal.regions.DOMRegionContext;
import org.eclipse.wst.xml.ui.internal.contentassist.ContentAssistRequest;
import org.eclipse.wst.xml.ui.internal.contentassist.XMLContentAssistProcessor;
import org.eclipse.wst.xml.ui.internal.contentassist.XMLRelevanceConstants;

@SuppressWarnings( { "restriction", "unchecked" })//$NON-NLS-1$ //$NON-NLS-2$
public class DITAContentAssistProcessor extends XMLContentAssistProcessor {

	/**
	 * @see org.eclipse.wst.xml.ui.internal.contentassist.XMLContentAssistProcessor#addAttributeValueProposals(org.eclipse.wst.xml.ui.internal.contentassist.ContentAssistRequest)
	 */
	@Override
	protected void addAttributeValueProposals(
			ContentAssistRequest contentAssistRequest) {

		IDOMNode node = (IDOMNode) contentAssistRequest.getNode();

		// Find the attribute region and name for which this position should
		// have a value proposed
		IStructuredDocumentRegion open = node.getFirstStructuredDocumentRegion();
		ITextRegionList openRegions = open.getRegions();
		int i = openRegions.indexOf(contentAssistRequest.getRegion());
		if (i >= 0) {
			ITextRegion nameRegion = null;

			while (i >= 0) {
				nameRegion = openRegions.get(i--);
				if (nameRegion.getType() == DOMRegionContext.XML_TAG_ATTRIBUTE_NAME) {
					break;
				}
			}

			// the name region is REQUIRED to do anything useful
			if (nameRegion != null) {
				// String attributeName = nameRegion.getText();
				String attributeName = open.getText(nameRegion);

				if ("conref".equals(attributeName) //$NON-NLS-1$
						|| "href".equals(attributeName) //$NON-NLS-1$
						|| "mapref".equals(attributeName)) { //$NON-NLS-1$
					try {
						boolean existingComplicatedValue = (contentAssistRequest.getRegion() != null)
								&& (contentAssistRequest.getRegion() instanceof ITextRegionContainer);

						if (!existingComplicatedValue) {
							String matchString = contentAssistRequest.getMatchString();

							if (matchString == null) {
								matchString = ""; //$NON-NLS-1$
							}

							if ((matchString.length() > 0)
									&& (matchString.startsWith("\"") || matchString.startsWith("'"))) { //$NON-NLS-1$ //$NON-NLS-2$
								matchString = matchString.substring(1);
							}

							IPath baseLocation = new Path(
									node.getModel().getBaseLocation());
							baseLocation = baseLocation.removeLastSegments(1);

							List<IPath> possibleValues = getPossibleValues(
									baseLocation, matchString);

							if (possibleValues.size() > 0) {
								LabelProvider labelProvider = new WorkbenchLabelProvider();
								IWorkspaceRoot root = ResourcesPlugin.getWorkspace().getRoot();

								for (IPath candidate : possibleValues) {
									String path = candidate.toString();
									IResource res = root.findMember(baseLocation.append(candidate));

									String rString = "\"" + path + "\""; //$NON-NLS-2$//$NON-NLS-1$
									int rOffset = contentAssistRequest.getReplacementBeginPosition();
									int rLength = contentAssistRequest.getReplacementLength();
									int cursorAfter = path.length() + 1;

									CustomCompletionProposal proposal = new CustomCompletionProposal(
											rString,
											rOffset,
											rLength,
											cursorAfter,
											labelProvider.getImage(res),
											candidate.lastSegment(),
											null,
											null,
											XMLRelevanceConstants.R_XML_ATTRIBUTE_VALUE);
									contentAssistRequest.addProposal(proposal);
								}
							}
						}
					} catch (CoreException e) {
						setErrorMessage(e.getLocalizedMessage());
						Activator.getDefault().getLog().log(e.getStatus());
					}
				}
			}
		}

		super.addAttributeValueProposals(contentAssistRequest);
	}

	private List<IPath> getPossibleValues(IPath baseLocation,
			final String matchString) throws CoreException {
		final List<IPath> candidates = new ArrayList<IPath>();

		IPath matchPath = new Path(matchString);
		String matchName = ""; //$NON-NLS-1$

		if (matchPath.segmentCount() > 0) {
			if (!matchPath.hasTrailingSeparator()) {
				matchName = matchPath.lastSegment();
				matchPath = matchPath.removeLastSegments(1);
			}
		}

		IPath basePath = baseLocation.append(matchPath);
		IWorkspaceRoot root = ResourcesPlugin.getWorkspace().getRoot();

		final IResource baseContainer = root.findMember(basePath);

		if (baseContainer != null) {
			final IPath path = matchPath;
			final String name = matchName;

			baseContainer.accept(new IResourceVisitor() {

				public boolean visit(IResource resource) throws CoreException {
					if (!resource.equals(baseContainer))
						if (name.length() == 0) {
							// Hide resources which name starts with a dot
							// unless the user actually started to type it
							if (!resource.getName().startsWith(".")) { //$NON-NLS-1$
								match(resource);
							}
						} else if (resource.getName().startsWith(name)) {
							match(resource);
						}

					return true;
				}

				private void match(IResource resource) {
					IPath match = path.append(resource.getName());

					if (resource instanceof IFolder) {
						match = match.addTrailingSeparator();
					}

					candidates.add(match);
				}
			}, IResource.DEPTH_ONE, false);
		}

		return candidates;
	}
}