package org.dita_op.editor.internal.validation;

import java.util.List;

import org.eclipse.core.resources.IFile;
import org.eclipse.core.resources.IResource;
import org.eclipse.wst.sse.core.StructuredModelManager;
import org.eclipse.wst.sse.core.internal.provisional.IStructuredModel;
import org.eclipse.wst.sse.core.internal.provisional.text.IStructuredDocument;
import org.eclipse.wst.sse.core.internal.provisional.text.IStructuredDocumentRegion;
import org.eclipse.wst.validation.internal.core.ValidationException;
import org.eclipse.wst.validation.internal.provisional.core.IMessage;
import org.eclipse.wst.validation.internal.provisional.core.IReporter;
import org.eclipse.wst.validation.internal.provisional.core.IValidationContext;
import org.eclipse.wst.validation.internal.provisional.core.IValidator;
import org.eclipse.wst.xml.core.internal.validation.core.Helper;

@SuppressWarnings( { "restriction", "unchecked" })
public class MarkupValidator extends
		org.eclipse.wst.xml.ui.internal.validation.MarkupValidator {

	public MarkupValidator() {
	}

	/**
	 * @see org.eclipse.wst.xml.ui.internal.validation.MarkupValidator#validate(org.eclipse.wst.validation.internal.provisional.core.IValidationContext,
	 *      org.eclipse.wst.validation.internal.provisional.core.IReporter)
	 */
	@Override
	public void validate(IValidationContext helper, IReporter reporter)
			throws ValidationException {
		String[] uris = helper.getURIs();

		if (uris.length > 0) {
			for (String uri : uris) {
				IFile file = (IFile) helper.loadModel(Helper.GET_FILE,
						new Object[] { uri });
				validate(file, reporter);
			}
		} else {
			List<IFile> files = (List<IFile>) helper.loadModel(
					Helper.GET_PROJECT_FILES,
					new Object[] { getClass().getName() });

			for (IFile file : files) {
				validate(file, reporter);
			}
		}
	}

	public void validate(IFile file, IReporter reporter)
			throws ValidationException {
		IStructuredModel model = null;

		try {
			model = StructuredModelManager.getModelManager().getExistingModelForRead(
					file);

			if (model != null) {
				IStructuredDocument document = model.getStructuredDocument();
				connect(document);

				// remove old messages
				reporter.removeAllMessages(this, file);

				IStructuredDocumentRegion[] regions = document.getStructuredDocumentRegions();
				for (int i = 0; i < regions.length && !reporter.isCancelled(); i++) {
					validate(regions[i], new MyReporter(reporter, file));
				}
			}
		} finally {
			if (model != null) {
				model.releaseFromRead();
			}
		}
	}

	private static class MyReporter implements IReporter {

		private final IReporter delegate;
		private final IResource target;

		public MyReporter(IReporter delegate, IResource target) {
			this.delegate = delegate;
			this.target = target;
		}

		public void addMessage(IValidator origin, IMessage message) {
			message.setTargetObject(target);
			delegate.addMessage(origin, message);
		}

		public void displaySubtask(IValidator validator, IMessage message) {
			message.setTargetObject(target);
			delegate.displaySubtask(validator, message);
		}

		public List getMessages() {
			return delegate.getMessages();
		}

		public boolean isCancelled() {
			return delegate.isCancelled();
		}

		public void removeAllMessages(IValidator origin) {
			delegate.removeAllMessages(origin);
		}

		public void removeAllMessages(IValidator origin, Object object) {
			delegate.removeAllMessages(origin, object);
		}

		public void removeMessageSubset(IValidator validator, Object obj,
				String groupName) {
			delegate.removeMessageSubset(validator, obj, groupName);
		}

	}
}
