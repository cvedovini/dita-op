package org.dita_op.editor.internal;

import java.net.URI;

import com.ibm.icu.text.Normalizer;

public class Utils {

	public static URI relativize(URI in, URI base) {
		String baseStr = base.normalize().toString();
		String inStr = in.normalize().toString();

		int baseLen = baseStr.length();
		int inLen = inStr.length();
		int i = 0;
		int sl = 0;
		for (; i < baseLen && i < inLen; i++) {
			char cb = baseStr.charAt(i);
			char ci = inStr.charAt(i);
			if (cb != ci) break;
			if (cb == '/') sl = i;
		}

		baseStr = baseStr.substring(sl + 1);
		inStr = inStr.substring(sl + 1);

		StringBuilder sbuf = new StringBuilder();
		sl = 0;
		for (i = baseLen = baseStr.length(); --i >= 0;) {
			if (baseStr.charAt(i) == '/') {
				sbuf.append("../"); //$NON-NLS-1$
			}
		}

		sbuf.append(inStr);
		return URI.create(sbuf.toString());
	}

	public static String slugify(String s) {
		// First is unicode canonical decomposition
		s = Normalizer.normalize(s, Normalizer.NFD);
		// Then remove non-ascii characters
		s = s.replaceAll("[^\\p{ASCII}]", ""); //$NON-NLS-1$ //$NON-NLS-2$
		// replace whitespaces
		s = s.replaceAll("\\s", "_"); //$NON-NLS-1$ //$NON-NLS-2$
		// finally lower case
		return s.toLowerCase();
	}
}
