from watchdog.observers import Observer
from watchdog.events import FileSystemEventHandler
import time, os, Queue, threading, subprocess

def shellquote(s):
	return "'" + s.replace("'", "'\\''") + "'"

class FileNotifications(FileSystemEventHandler):
	def catch_all_handler(self, event):
		if ((event.is_directory and event.src_path == local_dir) == False):
			changed_files.put(self.f)
	
	def on_moved(self, event):
		self.f = FileCommand(FileCommand.MOVE, event.src_path, event.dest_path)
		self.catch_all_handler(event)
	
	def on_created(self, event):
		self.f = FileCommand(FileCommand.UPDATE, event.src_path)
		self.catch_all_handler(event)
	
	def on_deleted(self, event):
		self.f = FileCommand(FileCommand.DELETE, event.src_path)
		self.catch_all_handler(event)
	
	def on_modified(self, event):
		self.f = FileCommand(FileCommand.UPDATE, event.src_path)
		self.catch_all_handler(event)

class FileCommand (threading.Thread):
	MOVE = 1
	DELETE = 2
	UPDATE = 3

	def __init__(self, action, src_path, dest_path = ''):
		super(FileCommand, self).__init__()

		if (action != self.MOVE and action != self.DELETE and action != self.UPDATE):
			raise ValueError('Must specify a valid action.')
		self.action = action

		if (src_path is None):
			raise ValueError('Must specify a file path.')
		self.path = src_path
		
		if (action == self.MOVE and dest_path is None):
			raise ValueError('Must specify a destination path (dest_path) when moving files.')
		self.dest_path = dest_path
	
	def run(self):
		if (self.action == self.UPDATE and os.path.exists(self.path) == False):
			return

		l = len(local_dir)
		f = remote_dir+self.path[l:]
		ef = shellquote(f)

		if (self.action == self.MOVE):
			print 'Move '+self.path[l:]+' to '+self.dest_path[l:]
			cmd = ['ssh', remote_host, 'if [ -e '+ef+' ]; then mv '+ef+' '+remote_dir+self.dest_path[l:]+'; fi']
		elif (self.action == self.DELETE):
			print 'Delete '+self.path[l:]
			cmd = ['ssh', remote_host, 'if [ -e '+ef+' ]; then rm '+ef+'; fi']
		elif (self.action == self.UPDATE):
			print 'Update '+self.path[l:]
			cmd = ['rsync', self.path, '-e', 'ssh', remote_host+':/'+f]
		else:
			raise ValueError('Must specify a destination path (dest_path) when moving files.')

		if cmd is not None:
			subprocess.call(cmd)

class Sync (threading.Thread):
	def run (self):
		while True:
			f = changed_files.get()
			f.run()
			changed_files.task_done()

remote_host = 'your-remote-server'
remote_dir = '/home/user/hole'
local_dir = '/Users/user/hol'

changed_files = Queue.Queue(4)
event_handler = FileNotifications()
Sync().start()

observer = Observer()
observer.schedule(event_handler, local_dir)
observer.start()
try:
    while True:
		time.sleep(1)
except KeyboardInterrupt:
	observer.stop()
observer.join()