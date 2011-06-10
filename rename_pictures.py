import os, time
from PIL import Image
from PIL.ExifTags import TAGS

def get_exif_data(fname):
  """Get embedded EXIF data from image file."""
  ret = {}
  try:
    img = Image.open(fname)
    if hasattr( img, '_getexif' ):
      exifinfo = img._getexif()
      if exifinfo != None:
        for tag, value in exifinfo.items():
          decoded = TAGS.get(tag, tag)
          ret[decoded] = value
  except IOError:
    print 'IOERROR ' + fname
  return ret

def rename_photos(dir):
  for file in os.listdir(dir):
    f = dir + '/' + file

    # If file is a directory, rename the files in it.
    if os.path.isdir(f):
      rename_photos(f)
      continue

    # Grab the file extension
    (shortname, ext) = os.path.splitext(f) 
    
    ftime = ''
    # If the file is a jpg
    if ext.lower() in ['.jpg', '.jpeg']:
      data = get_exif_data(f)

      # If the image has a date stamp
      if 'DateTime' in data:
        ftime = data['DateTime']
      elif 'DateTimeOriginal' in data:
        ftime = data['DateTimeOriginal']
      ftime = ftime.replace(':', '-')
      ftime = ftime.replace(' ', '-')

    # If time is still empty, use file modification time
    if ftime == '':
      t = os.path.getmtime(f)
      ftime = time.strftime('%Y-%m-%d-%H-%M-%S', time.gmtime(t))
    
    os.rename(f, dir + '/' + ftime + ext)

if __name__ == "__main__":
  import sys
  if len(sys.argv) > 1:
    rename_photos(sys.argv[1])
  else:
    print "usage: rename_photos.py directory_of_photos_to_rename_by_timestamp\n"
