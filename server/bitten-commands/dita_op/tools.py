# -*- coding: utf-8 -*-
##
#  Copyright (C) 2008 Claude Vedovini <http://vedovini.net/>.
#
#  This file is part of the DITA Open Platform <http://www.dita-op.org/>.
#
#  The DITA Open Platform is free software: you can redistribute it and/or modify
#  it under the terms of the GNU General Public License as published by
#  the Free Software Foundation, either version 3 of the License, or
#  (at your option) any later version.
#
#  The DITA Open Platform is distributed in the hope that it will be useful,
#  but WITHOUT ANY WARRANTY; without even the implied warranty of
#   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#  GNU General Public License for more details.
#
#  You should have received a copy of the GNU General Public License
#  along with The DITA Open Platform.  If not, see <http://www.gnu.org/licenses/>.
##

import re
from xml.dom import minidom
from bitten.build import shtools
import logging
import os
import posixpath
import shlex
import tempfile

log = logging.getLogger('dita_op.tools')

__docformat__ = 'restructuredtext en'

def dita(ctx, config, output_dir=None, quiet=False, keep_going=False):
    """Runs a DITA-OT transformation.
    
    :param ctx: the build context
    :type ctx: `Context`
    :param config: the Eclipse launch configuration containing the build parameters
    :param keep_going: whether the toolkit should keep going when errors are encountered (optional, defaults to False)
    """
    root = minidom.parse(ctx.resolve(config)).documentElement
    
    if root.attributes['type'].value != 'org.dita_op.dost.launcher.DOSTLaunchConfigurationType':
        ctx.error('Not a valid launch configuration')
        return
    
    ditamap = None
    ditaval = None
    transtype = 'xhtml'
    args = []
    vmargs = None
    
    for node in root.getElementsByTagName('stringAttribute'):
        if node.getAttribute('key') == 'transtype':
            transtype = node.getAttribute('value')
        elif node.getAttribute('key') == 'args.input':
            ditamap = workspace_resolve(ctx, node.getAttribute('value'))
        elif node.getAttribute('key') == 'dita.input.valfile':
            ditaval = workspace_resolve(ctx, node.getAttribute('value'))
        elif node.getAttribute('key') == 'org.eclipse.jdt.launching.VM_ARGUMENTS':
            vmargs = node.getAttribute('value')
    
    for node in root.getElementsByTagName('mapAttribute'):
        if node.attributes['key'].value == 'other.args':
            for entry in node.getElementsByTagName('mapEntry'):
                value = workspace_resolve(ctx, entry.getAttribute('value'))
                args.append('-D%s=%s' % ( entry.getAttribute('key'), ctx.resolve(value) ))

    if not ditamap:
        ctx.error('No ditamap provided')
        return

    configname = os.path.basename(os.path.splitext(config)[0])
    tempdir = os.path.join(os.getenv('TEMP', 'C:\\temp'), 'dop', configname)

    if not output_dir:
        output_dir = os.path.join(os.environ['DOP_HOME'], 'output', os.path.basename(ctx.basedir), configname)

    dita2(ctx, ditamap, output_dir, transtype, ditaval, tempdir, quiet, keep_going, args, vmargs)

def workspace_resolve(ctx, path):
    result = path
    m = re.match('^\${resource_loc:/(.*)}$', path)

    if m:
        result = m.group(1)
    
    return result

def dita2(ctx, ditamap, output_dir, transtype='xhtml', ditaval=None, tempdir=None, quiet=False, keep_going=False, args=None, vmargs=None):
    """Runs a DITA-OT transformation.
    
    :param ctx: the build context
    :type ctx: `Context`
    :param ditamap: path to the ditamap file
    :param transtype: name of the transformation that should be run (optional, defaults to 'xhtml')
    :param ditaval: path to the processing profile (optional)
    :param output_dir: destination of the output (optional)
    :param keep_going: whether the toolkit should keep going when errors are encountered (optional, defaults to False)
    :param args: additional arguments to pass to the toolkit (optional)
    """
    dita_home = os.environ['DITA_HOME']
    dita_lib = os.path.join(dita_home, 'lib')
    build_file = os.path.join(dita_home, 'build.xml')

    if not tempdir:
        tempdir = 'temp'
        
    if not args:    
        args = []

    if quiet:
        quiet = quiet.lower()
        if quiet == 'true' or quiet == 'yes':
            args += ['-quiet']

    if ditaval:
        args += ['-Ddita.input.valfile=%s' % ditaval]

    args += ['-Ddita.dir=%s' % dita_home]
    args += ['-Dargs.input=%s' % ctx.resolve(ditamap)]
    args += ['-Dtranstype=%s' % transtype]
    args += ['-Ddita.temp.dir=%s' % ctx.resolve(tempdir)]
    args += ['-Dclean.temp=yes']
    args += ['-Doutput.dir=%s' % ctx.resolve(output_dir)]
    args += ['-lib', dita_lib]

    os.environ['ANT_OPTS'] = vmargs or ''
    ant(ctx, build_file, 'init', keep_going, args)
    del os.environ['ANT_OPTS']


def ant(ctxt, file_=None, target=None, keep_going=False, args=None):
    """Run an Ant build.
    
    :param ctxt: the build context
    :type ctxt: `Context`
    :param file\_: name of the Ant build file
    :param target: name of the target that should be executed (optional)
    :param keep_going: whether Ant should keep going when errors are encountered
    :param args: additional arguments to pass to Ant
    """
    executable = 'ant'
    ant_home = os.environ.get('ANT_HOME')
    if ant_home:
        executable = os.path.join(ant_home, 'bin', 'ant')

    if not args:
        args = []
        
    args += ['-noinput']
    
    if file_:
        args += ['-buildfile', ctxt.resolve(file_)]
        
    if keep_going:
        args += ['-keep-going']

    if target:
        args += [target]

    returncode = shtools.execute(ctxt, file_=executable, args=args)
    if returncode != 0:
        ctxt.error('Ant failed (%s)' % returncode)
