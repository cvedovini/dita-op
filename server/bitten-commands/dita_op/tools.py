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

from glob import glob
import logging
import os
from bitten.tools.javatools import ant

def dita(ctx, ditamap, transtype='xhtml', output_dir=None, keep_going=False, args=None):
    """Run a DITA-OT transformation.
    
    :param ctx: the build context
    :type ctx: `Context`
    :param ditamap: name of the ditamap file
    :param transtype: name of the transformation that should be run (optional, defaults to 'xhtml')
    :param output_dir: destination of the output (optional, defaults to 'transtype_output')
    :param keep_going: whether the toolkit should keep going when errors are encountered (optional, defaults to False)
    :param args: additional arguments to pass to the toolkit (optional)
    """
    dita_home = os.environ['DITA_HOME']
    build_file = os.path.join(dita_home, 'build.xml')
    
    if not args:    
        args = ''
        
    args += ' -Dargs.input=' + ctx.resolve(ditamap)
    args += ' -Dtranstype=' + transtype
    args += ' -Ddita.temp.dir=' + os.tempnam()
    
    if output_dir:
        args += ' -Doutput.dir=' + ctx.resolve(output_dir)
    else:
        args += ' -Doutput.dir=' + ctx.resolve(transtype + '_output')
    
    ant(ctx, build_file, 'init', keep_going, args)
