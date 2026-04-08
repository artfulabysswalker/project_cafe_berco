import urllib.request

targets = {
    'cake1': 'https://images.unsplash.com/photo-1499636136210-6f4ee915583e?q=80&w=400',
    'cake2': 'https://images.unsplash.com/photo-1554118811-1e0d58224f24?q=80&w=400',
    'cake3': 'https://images.unsplash.com/photo-1512428559087-560fa5ceab42?q=80&w=400'
}

for name, url in targets.items():
    try:
        data = urllib.request.urlopen(url, timeout=15).read()
        with open(f'{name}.jpg', 'wb') as f:
            f.write(data)
        print(name, 'OK', len(data))
    except Exception as e:
        print(name, 'ERR', e)
