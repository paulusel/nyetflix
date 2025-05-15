class Loader extends Hls.DefaultConfig.loader {
  constructor(config, token, url, videoId, pos) {
    super(config);
    this.token = token;
    this.url = url;
    this.videoId = videoId;
  }

  load(context, config, callbacks) {
    const requestData = {
      video_id: this.videoId,
      request_type: context.type,
      segment_info: null
    };

    if (context.type === 'segment') {
      requestData.segment_info = {
        sn: context.frag.sn,
        start: context.frag.start
      };
    }

    fetch(this.url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': 'Bearer ' + this.token
      },
      body: JSON.stringify(requestData)
    })
    .then(response => {
      if (!response.ok) {
          throw new Error('Request failed');
      }
      return context.responseType === 'text' ? response.text() : response.arrayBuffer();
    })
    .then(data => {
      callbacks.onSuccess({
        url: context.url,
        data: data
      }, context);
    })
    .catch(error => {
      callbacks.onError(error, context);
    });
  }
}

function getHLS(token, url, videoId, pos) {
  const config = {
    loader: (cfg) => new Loader(cfg, token, url, videoId),
    startPosition: pos,
    enableWorker: true,
    lowLatencyMode: true
  };

  return new Hls(config);
}
