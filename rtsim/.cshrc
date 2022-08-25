# @(#)Cshrc 1.5 90/11/01 SMI
#################################################################
#
#         .cshrc file
#
#         initial setup file for both interactive and noninteractive
#         C-Shells
#
#################################################################


# Set openwin as my default window system 
set mychoice=openwin

#         set up search path

# add directories for local commands
set lpath = ( )
if ( ${mychoice} != 0 ) then
# estou mudando ?mychoice para mychoice.
	if ( ${mychoice} == "openwin" ) then
		set lpath = ( /usr/openwin/bin/xview /usr/openwin/bin $lpath )
	endif
endif

set path = (. ~ ~/bin $lpath /usr/ucb /usr/bin /usr/sbin /usr/local/master/bin /usr/local/master $path /. )

#         cd path

#set lcd = ( )  #  add parents of frequently used directories
#set cdpath = (.. ~ ~/bin ~/src $lcd)

#         set this for all shells

set noclobber

#         aliases for all shells

#umask 002

#         skip remaining setup if not an interactive shell

if ($?USER == 0 || $?prompt == 0) exit

#          settings  for interactive shells

set history=30
set ignoreeof
#set notify
#set savehist=40
#set prompt="% "
set prompt="`hostname`{`whoami`}\!: "
#set time=100

#          commands for interactive shells

alias edit '/usr/dt/bin/dtpad -Wb 240 240 240 -scale large \!* &'

#date
#pwd

#         other aliases

#alias a            alias
#alias u            unalias

#alias             clear
#alias list         cat
#alias lock          lockscreen
#alias m             more

alias .             'echo $cwd'
alias ..            'set dot=$cwd;cd ..'
alias ,             'cd $dot '
alias rm            'rm -r' 
alias bye           'clear;logout'


alias pdw           'echo $cwd'
#alias la            'ls -a'
#alias ll            'ls -la'
alias ls           'ls -F'

#alias pd           dirs
#alias po           popd
#alias pp           pushd

#alias +w            'chmod go+w'
#alias -w            'chmod go-w'
#alias x             'chmod +x'

#alias nms 'tbl \!* | nroff -ms | more'                  # nroff -ms
#alias tms 'tbl \!* | troff -t -ms >! troff.output &'    # troff -ms
#alias tpr 'tbl \!* | troff -t -ms | lpr -t &'           # troff & print
#alias ppr 'lpr -t \!* &'                                # print troffed

#alias lp1           'lpr -P1'
#alias lq1           'lpq -P1'
#alias lr1           'lprm -P1'
alias print          'lpr -h'
