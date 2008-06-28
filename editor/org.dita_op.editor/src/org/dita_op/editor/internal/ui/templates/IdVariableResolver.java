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

import java.text.SimpleDateFormat;
import java.util.Date;

import org.eclipse.jface.text.templates.TemplateContext;
import org.eclipse.jface.text.templates.TemplateVariableResolver;

public class IdVariableResolver extends TemplateVariableResolver {

	public IdVariableResolver() {
	}

	public IdVariableResolver(String type, String description) {
		super(type, description);
	}

	/**
	 * @see org.eclipse.jface.text.templates.TemplateVariableResolver#resolve(org.eclipse.jface.text.templates.TemplateContext)
	 */
	@Override
	protected String resolve(TemplateContext context) {
		SimpleDateFormat sdf = new SimpleDateFormat("yyyyMMddHHmmss"); //$NON-NLS-1$
		return "id" + sdf.format(new Date()); //$NON-NLS-1$
	}

	/**
	 * @see org.eclipse.jface.text.templates.TemplateVariableResolver#isUnambiguous(org.eclipse.jface.text.templates.TemplateContext)
	 */
	@Override
	protected boolean isUnambiguous(TemplateContext context) {
		return true;
	}

}
