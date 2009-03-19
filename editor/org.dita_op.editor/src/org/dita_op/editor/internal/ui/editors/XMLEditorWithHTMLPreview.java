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
import java.io.StringReader;
import java.io.StringWriter;

import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.parsers.ParserConfigurationException;
import javax.xml.transform.ErrorListener;
import javax.xml.transform.Transformer;
import javax.xml.transform.TransformerException;
import javax.xml.transform.dom.DOMSource;
import javax.xml.transform.stream.StreamResult;

import org.dita_op.editor.internal.Activator;
import org.eclipse.core.resources.IMarker;
import org.eclipse.core.resources.IResourceChangeEvent;
import org.eclipse.core.resources.IResourceChangeListener;
import org.eclipse.core.resources.ResourcesPlugin;
import org.eclipse.core.runtime.IProgressMonitor;
import org.eclipse.core.runtime.IStatus;
import org.eclipse.jface.dialogs.ErrorDialog;
import org.eclipse.jface.text.IDocument;
import org.eclipse.swt.SWT;
import org.eclipse.swt.browser.Browser;
import org.eclipse.swt.browser.LocationAdapter;
import org.eclipse.swt.browser.LocationEvent;
import org.eclipse.swt.browser.StatusTextEvent;
import org.eclipse.swt.browser.StatusTextListener;
import org.eclipse.swt.widgets.Display;
import org.eclipse.swt.widgets.Text;
import org.eclipse.ui.IEditorInput;
import org.eclipse.ui.IEditorPart;
import org.eclipse.ui.IEditorSite;
import org.eclipse.ui.IURIEditorInput;
import org.eclipse.ui.IWorkbenchPage;
import org.eclipse.ui.PartInitException;
import org.eclipse.ui.ide.IDE;
import org.eclipse.ui.part.FileEditorInput;
import org.eclipse.ui.part.MultiPageEditorPart;
import org.eclipse.ui.part.MultiPageEditorSite;
import org.eclipse.wst.common.uriresolver.internal.ExtensibleURIResolver;
import org.eclipse.wst.sse.ui.StructuredTextEditor;
import org.eclipse.wst.xml.core.internal.provisional.contenttype.ContentTypeIdForXML;
import org.w3c.dom.Document;
import org.xml.sax.EntityResolver;
import org.xml.sax.ErrorHandler;
import org.xml.sax.InputSource;
import org.xml.sax.SAXException;
import org.xml.sax.SAXParseException;

@SuppressWarnings("restriction")//$NON-NLS-1$
public abstract class XMLEditorWithHTMLPreview extends MultiPageEditorPart
		implements IResourceChangeListener, EntityResolver {

	/** The xml sourceEditor used in page 0. */
	private StructuredTextEditor sourceEditor;
	private Text htmlEditor;

	/** The browser widget used in page 1. */
	private Browser browser;

	private String baseLocation;

	private int sourcePageIndex = -1;
	private int previewPageIndex = -1;
	private int htmlPageIndex = -1;

	private PreviewErrorHandler errorHandler = new PreviewErrorHandler();
	private ExtensibleURIResolver uriResolver = new ExtensibleURIResolver();
	private Transformer transformer = null;

	/**
	 * Creates a multi-page sourceEditor example.
	 */
	public XMLEditorWithHTMLPreview() {
		super();
		ResourcesPlugin.getWorkspace().addResourceChangeListener(this);
	}

	/**
	 * Saves the multi-page sourceEditor's document.
	 */
	@Override
	public void doSave(IProgressMonitor monitor) {
		sourceEditor.doSave(monitor);
	}

	/**
	 * Saves the multi-page sourceEditor's document as another file. Also
	 * updates the text for page 0's tab, and updates this multi-page
	 * sourceEditor's input to correspond to the nested sourceEditor's.
	 */
	@Override
	public void doSaveAs() {
		sourceEditor.doSaveAs();
		IEditorInput input = sourceEditor.getEditorInput();
		setPartName(input.getName());
		setTitleToolTip(input.getToolTipText());
		setInput(input);
	}

	public void gotoMarker(IMarker marker) {
		setActiveEditor(sourceEditor);
		IDE.gotoMarker(sourceEditor, marker);
	}

	/**
	 * The <code>MultiPageEditorExample</code> implementation of this method
	 * checks that the input is an instance of <code>IFileEditorInput</code>.
	 */
	@Override
	public void init(IEditorSite site, IEditorInput editorInput)
			throws PartInitException {
		setPartName(editorInput.getName());
		setTitleToolTip(editorInput.getToolTipText());

		if (editorInput instanceof IURIEditorInput) {
			baseLocation = ((IURIEditorInput) editorInput).getURI().toString();
		} else {
			throw new PartInitException(
					Messages.getString("XMLEditorWithHTMLPreview.invalidInput")); //$NON-NLS-1$

		}

		super.init(site, editorInput);
	}

	/**
	 * The <code>MultiPageEditorPart</code> implementation of this
	 * <code>IWorkbenchPart</code> method disposes all nested editors.
	 * Subclasses may extend.
	 */
	@Override
	public void dispose() {
		ResourcesPlugin.getWorkspace().removeResourceChangeListener(this);
		super.dispose();
	}

	/**
	 * @see org.eclipse.ui.part.EditorPart#isSaveAsAllowed()
	 */
	@Override
	public boolean isSaveAsAllowed() {
		return true;
	}

	/**
	 * Closes all project files on project close.
	 */
	public void resourceChanged(final IResourceChangeEvent event) {
		if (event.getType() == IResourceChangeEvent.PRE_CLOSE) {
			Display.getDefault().asyncExec(new Runnable() {
				public void run() {
					IWorkbenchPage[] pages = getSite().getWorkbenchWindow().getPages();

					for (int i = 0; i < pages.length; i++) {
						if (((FileEditorInput) getEditorInput()).getFile().getProject().equals(
								event.getResource())) {
							IEditorPart editorPart = pages[i].findEditor(getEditorInput());
							pages[i].closeEditor(editorPart, true);
						}
					}
				}
			});
		}
	}

	public InputSource resolveEntity(String publicId, String systemId)
			throws SAXException, IOException {
		String location = uriResolver.resolve(baseLocation, publicId, systemId);
		return (location == null) ? null : new InputSource(location);
	}

	@Override
	protected IEditorSite createSite(IEditorPart page) {
		IEditorSite site = null;

		if (page == sourceEditor) {
			site = new MultiPageEditorSite(this, page) {
				@Override
				public String getId() {
					// Sets this ID so nested sourceEditor is configured for XML
					// source
					return ContentTypeIdForXML.ContentTypeID_XML + ".source"; //$NON-NLS-1$;
				}
			};
		} else {
			site = super.createSite(page);
		}

		return site;
	}

	/**
	 * Creates the pages of the multi-page sourceEditor.
	 */
	@Override
	protected void createPages() {
		createSourcePage();
		createPreviewPage();
		// createHtmlPage();
	}

	/**
	 * Creates page 1 of the multi-page sourceEditor, which contains a text
	 * sourceEditor.
	 */
	protected void createSourcePage() {
		try {
			sourceEditor = new StructuredTextEditor();
			sourcePageIndex = addPage(sourceEditor, getEditorInput());
			setPageText(
					sourcePageIndex,
					Messages.getString("XMLEditorWithHTMLPreview.sourcePageTitle")); //$NON-NLS-1$
		} catch (PartInitException e) {
			ErrorDialog.openError(
					getSite().getShell(),
					Messages.getString("XMLEditorWithHTMLPreview.errorCreatingSourcePage"), null, e.getStatus()); //$NON-NLS-1$
		}
	}

	/**
	 * Creates page 2 of the multi-page sourceEditor, which shows the sorted
	 * text.
	 */
	protected void createPreviewPage() {
		browser = new Browser(getContainer(), SWT.NONE);
		browser.addLocationListener(new LocationAdapter() {

			@Override
			public void changing(LocationEvent event) {
				event.doit = navigate(event.location);
			}
		});

		browser.addStatusTextListener(new StatusTextListener() {

			public void changed(StatusTextEvent event) {
				getEditorSite().getActionBars().getStatusLineManager().setMessage(
						event.text);
			}
		});

		previewPageIndex = addPage(browser);
		setPageText(previewPageIndex,
				Messages.getString("XMLEditorWithHTMLPreview.previewPageTitle")); //$NON-NLS-1$
	}

	/**
	 * Creates page 1 of the multi-page sourceEditor, which contains a text
	 * sourceEditor.
	 */
	protected void createHtmlPage() {
		htmlEditor = new Text(getContainer(), SWT.MULTI | SWT.READ_ONLY
				| SWT.V_SCROLL);
		htmlPageIndex = addPage(htmlEditor);
		setPageText(htmlPageIndex,
				Messages.getString("XMLEditorWithHTMLPreview.htmlPageTitle")); //$NON-NLS-1$
	}

	/**
	 * Calculates the contents of page 2 when previewPageIndex is activated.
	 */
	@Override
	protected void pageChange(int newPageIndex) {
		super.pageChange(newPageIndex);

		if (newPageIndex == previewPageIndex) {
			browser.getDisplay().asyncExec(new Runnable() {

				public void run() {
					generatePreview();
				}
			});
		} else if (newPageIndex == htmlPageIndex) {
			browser.getDisplay().asyncExec(new Runnable() {

				public void run() {
					generateHtml();
				}
			});
		}
	}

	protected abstract Transformer createTransformer() throws IOException,
			TransformerException;

	protected void log(int severity, Exception e) {
		Activator.getDefault().log(severity, e);
	}

	private void generatePreview() {
		IDocument doc = sourceEditor.getTextViewer().getDocument();
		String asText = doc.get();

		StringWriter writer = new StringWriter();

		try {
			DocumentBuilderFactory bf = DocumentBuilderFactory.newInstance();
			bf.setValidating(false);

			DocumentBuilder builder = bf.newDocumentBuilder();
			builder.setEntityResolver(this);

			builder.setErrorHandler(errorHandler);
			Document asDOM = builder.parse(new InputSource(new StringReader(
					asText)));

			if (transformer == null) {
				transformer = createTransformer();
				transformer.setErrorListener(errorHandler);
			}

			transformer.transform(new DOMSource(asDOM),
					new StreamResult(writer));
		} catch (IOException e) {
			log(IStatus.ERROR, e);
		} catch (ParserConfigurationException e) {
			log(IStatus.ERROR, e);
		} catch (SAXException e) {
			log(IStatus.WARNING, e);
		} catch (TransformerException e) {
			log(IStatus.WARNING, e);
		}

		browser.setText(writer.toString());
	}

	private void generateHtml() {
		IDocument doc = sourceEditor.getTextViewer().getDocument();
		String asText = doc.get();

		StringWriter writer = new StringWriter();

		try {
			DocumentBuilderFactory bf = DocumentBuilderFactory.newInstance();
			bf.setValidating(false);

			DocumentBuilder builder = bf.newDocumentBuilder();
			builder.setEntityResolver(this);

			builder.setErrorHandler(errorHandler);
			Document asDOM = builder.parse(new InputSource(new StringReader(
					asText)));

			if (transformer == null) {
				transformer = createTransformer();
				transformer.setErrorListener(errorHandler);
			}

			transformer.transform(new DOMSource(asDOM),
					new StreamResult(writer));
		} catch (IOException e) {
			log(IStatus.ERROR, e);
		} catch (ParserConfigurationException e) {
			log(IStatus.ERROR, e);
		} catch (SAXException e) {
			log(IStatus.WARNING, e);
		} catch (TransformerException e) {
			log(IStatus.WARNING, e);
		}

		htmlEditor.setText(writer.toString());
	}

	private boolean navigate(String location) {
		// TODO: Open the referenced dita file if possible
		return "about:blank".equals(location); //$NON-NLS-1$
	}

	/**
	 * An error handler that ignores all problems.
	 */
	private final class PreviewErrorHandler implements ErrorHandler,
			ErrorListener {

		public void error(SAXParseException e) throws SAXException {
		}

		public void fatalError(SAXParseException e) throws SAXException {
		}

		public void warning(SAXParseException e) throws SAXException {
		}

		public void error(TransformerException e) throws TransformerException {
		}

		public void fatalError(TransformerException e)
				throws TransformerException {
		}

		public void warning(TransformerException e) throws TransformerException {
		}

	}

}
