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
package org.dita_op.editor.internal.ui.templates;

import java.util.Properties;
import java.util.Set;
import java.util.Map.Entry;

import org.eclipse.jface.text.BadLocationException;
import org.eclipse.jface.text.templates.ContextTypeRegistry;
import org.eclipse.jface.text.templates.Template;
import org.eclipse.jface.text.templates.TemplateBuffer;
import org.eclipse.jface.text.templates.TemplateContext;
import org.eclipse.jface.text.templates.TemplateContextType;
import org.eclipse.jface.text.templates.TemplateException;
import org.eclipse.jface.text.templates.TemplateTranslator;
import org.eclipse.jface.text.templates.persistence.TemplatePersistenceData;
import org.eclipse.wst.xml.ui.internal.XMLUIPlugin;
import org.eclipse.wst.xml.ui.internal.templates.TemplateContextTypeIdsXML;

public class DITATemplateContext extends TemplateContext {

	public DITATemplateContext(TemplateContextType contextType) {
		super(contextType);
	}

	@Override
	public boolean canEvaluate(Template template) {
		return true;
	}

	public TemplateBuffer evaluate(String template)
			throws BadLocationException, TemplateException {
		TemplateTranslator translator = new TemplateTranslator();
		TemplateBuffer buffer = translator.translate(template);

		getContextType().resolve(buffer, this);
		return buffer;
	}

	@Override
	public TemplateBuffer evaluate(Template template)
			throws BadLocationException, TemplateException {
		TemplateTranslator translator = new TemplateTranslator();
		TemplateBuffer buffer = translator.translate(template);

		getContextType().resolve(buffer, this);
		return buffer;
	}

	public static String evaluateTemplate(String templateId)
			throws BadLocationException, TemplateException {
		return evaluateTemplate(templateId, null);
	}

	public static String evaluateTemplate(String templateId,
			Properties variables) throws BadLocationException,
			TemplateException {
		ContextTypeRegistry registry = XMLUIPlugin.getDefault().getTemplateContextRegistry();
		TemplateContextType contextType = registry.getContextType(TemplateContextTypeIdsXML.ALL);
		TemplatePersistenceData data = XMLUIPlugin.getDefault().getTemplateStore().getTemplateData(
				templateId);

		DITATemplateContext ctx = new DITATemplateContext(contextType);

		if (variables != null) {
			Set<Entry<Object, Object>> entries = variables.entrySet();
			for (Entry<Object, Object> entry : entries) {
				ctx.setVariable((String) entry.getKey(),
						(String) entry.getValue());
			}
		}

		TemplateBuffer buffer = ctx.evaluate(data.getTemplate().getPattern());
		return buffer.getString();
	}
}