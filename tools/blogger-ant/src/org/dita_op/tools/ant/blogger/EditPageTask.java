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
package org.dita_op.tools.ant.blogger;

import java.io.BufferedReader;
import java.io.File;
import java.io.FileReader;
import java.io.IOException;
import java.net.URL;

import org.apache.tools.ant.BuildException;
import org.apache.tools.ant.Project;
import org.apache.tools.ant.Task;
import org.apache.xmlrpc.XmlRpcException;
import org.apache.xmlrpc.client.XmlRpcClient;
import org.apache.xmlrpc.client.XmlRpcClientConfigImpl;

public class EditPageTask extends Task {

	private static final String KEY = "dita-op.org";
	private static final int BLOG_ID = 1;

	private Integer postid;
	private String username;
	private String password;
	private String url;
	private String content;
	private File contentsrc;
	private boolean publish = true;
	private boolean stripspaces = true;

	public void execute() throws BuildException {
		if (url == null) {
			throw new BuildException("Service url must be provided");
		}

		if (username == null || password == null) {
			throw new BuildException("Username and password must be provided");
		}

		if (contentsrc == null && content == null) {
			throw new BuildException("Content must be provided");
		}

		try {
			if (contentsrc != null) {
				content = readContent(contentsrc);
			}

			if (stripspaces) {
				content = content.replaceAll("\\s+", " ");
			}

			getProject().log("Posting to " + url + " with user " + username);
			getProject().log(content, Project.MSG_VERBOSE);
			Object[] params = null;
			String methodName = null;

			if (postid != null) {
				methodName = "blogger.editPost";
				params = new Object[] { KEY, postid, username, password,
						content, publish };
			} else {
				methodName = "blogger.newPost";
				params = new Object[] { KEY, BLOG_ID, username, password,
						content, publish };
			}

			XmlRpcClientConfigImpl config = new XmlRpcClientConfigImpl();
			config.setServerURL(new URL(url));
			XmlRpcClient client = new XmlRpcClient();
			client.setConfig(config);

			Object result = client.execute(methodName, params);

			getProject().log("Returned: " + result);
		} catch (XmlRpcException e) {
			getProject().log(e.getMessage(), Project.MSG_ERR);
			throw new BuildException(e);
		} catch (IOException e) {
			getProject().log(e.getMessage(), Project.MSG_ERR);
			throw new BuildException(e);
		}
	}

	private String readContent(File contentsrc) throws IOException {
		getProject().log("Reading content from " + contentsrc);
		StringBuilder builder = new StringBuilder();
		BufferedReader reader = new BufferedReader(new FileReader(contentsrc));

		try {
			char[] cbuf = new char[512];

			for (int c = reader.read(cbuf); c > 0; c = reader.read(cbuf)) {
				builder.append(cbuf, 0, c);
			}
		} finally {
			reader.close();
		}

		return builder.toString();
	}

	public Integer getPostid() {
		return postid;
	}

	public void setPostid(int postid) {
		this.postid = Integer.valueOf(postid);
	}

	public String getUsername() {
		return username;
	}

	public void setUsername(String username) {
		this.username = username;
	}

	public String getPassword() {
		return password;
	}

	public void setPassword(String password) {
		this.password = password;
	}

	public String getUrl() {
		return url;
	}

	public void setUrl(String url) {
		this.url = url;
	}

	public String getContent() {
		return content;
	}

	public void setContent(String content) {
		this.content = content;
	}

	public void addText(String content) {
		this.content = getProject().replaceProperties(content);
	}

	public File getContentsrc() {
		return contentsrc;
	}

	public void setContentsrc(File contentsrc) {
		this.contentsrc = contentsrc;
	}

	public boolean isPublish() {
		return publish;
	}

	public void setPublish(boolean publish) {
		this.publish = publish;
	}

	public boolean isStripspaces() {
		return stripspaces;
	}

	public void setStripspaces(boolean stripspaces) {
		this.stripspaces = stripspaces;
	}

}
