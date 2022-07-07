const historyRouter = {
  match: null,
  history: null,
  init(routeMode = 'hash') {
    historyRouter.match = amisRequire('path-to-regexp').match;
    historyRouter.history = routeMode === 'history' ? History.createBrowserHistory() : History.createHashHistory()
  },
  normalizeLink(to, location = historyRouter.history.location) {
    to = to || '';

    if (to && to[0] === '#') {
      to = location.pathname + location.search + to;
    } else if (to && to[0] === '?') {
      to = location.pathname + to;
    }

    const idx = to.indexOf('?');
    const idx2 = to.indexOf('#');
    let pathname = ~idx
      ? to.substring(0, idx)
      : ~idx2
        ? to.substring(0, idx2)
        : to;
    let search = ~idx ? to.substring(idx, ~idx2 ? idx2 : undefined) : '';
    let hash = ~idx2 ? to.substring(idx2) : location.hash;

    if (!pathname) {
      pathname = location.pathname;
    } else if (pathname[0] !== '/' && !/^https?\:\/\//.test(pathname)) {
      let relativeBase = location.pathname;
      const paths = relativeBase.split('/');
      paths.pop();
      let m;
      while ((m = /^\.\.?\//.exec(pathname))) {
        if (m[0] === '../') {
          paths.pop();
        }
        pathname = pathname.substring(m[0].length);
      }
      pathname = paths.concat(pathname).join('/');
    }

    return pathname + search + hash;
  },
  isCurrentUrl(to, ctx) {
    if (!to) {
      return false;
    }
    const pathname = historyRouter.history.location.pathname;
    const link = historyRouter.normalizeLink(to, {
      ...window.location,
      pathname,
      hash: ''
    });

    if (!~link.indexOf('http') && ~link.indexOf(':')) {
      let strict = ctx && ctx.strict;
      return match(link, {
        decode: decodeURIComponent,
        strict: typeof strict !== 'undefined' ? strict : true
      })(pathname);
    }

    return decodeURI(pathname) === link;
  },
  updateLocation(location, replace) {
    location = historyRouter.normalizeLink(location);
    if (location === 'goBack') {
      return historyRouter.history.goBack();
    } else if (
      (!/^https?\:\/\//.test(location) &&
        location ===
        historyRouter.history.location.pathname + historyRouter.history.location.search) ||
      location === historyRouter.history.location.href
    ) {
      // 目标地址和当前地址一样，不处理，免得重复刷新
      return;
    } else if (/^https?\:\/\//.test(location) || !historyRouter.history) {
      return (window.location.href = location);
    }

    historyRouter.history[replace ? 'replace' : 'push'](location);
  },
  jumpTo(to, action) {
    if (to === 'goBack') {
      return historyRouter.history.goBack();
    }

    to = historyRouter.normalizeLink(to);

    if (historyRouter.isCurrentUrl(to)) {
      return;
    }

    if (action && action.actionType === 'url') {
      action.blank === false
        ? (window.location.href = to)
        : window.open(to, '_blank');
      return;
    } else if (action && action.blank) {
      window.open(to, '_blank');
      return;
    }

    if (/^https?:\/\//.test(to)) {
      window.location.href = to;
    } else if (
      (!/^https?\:\/\//.test(to) &&
        to === historyRouter.history.pathname + historyRouter.history.location.search) ||
      to === historyRouter.history.location.href
    ) {
      // do nothing
    } else {
      historyRouter.history.push(to);
    }
  }
}

historyRouter.init(routeMode)

window.amisAppProps = Object.assign({
  location: historyRouter.history.location,
}, window.amisAppProps || {});

window.amisAppEnv = Object.assign({
  updateLocation: historyRouter.updateLocation,
  jumpTo: historyRouter.jumpTo,
  isCurrentUrl: historyRouter.isCurrentUrl,
}, window.amisAppEnv || {});

const tmpAmisAppLoaded = window.amisAppLoaded
window.amisAppLoaded = (amisApp) => {
  tmpAmisAppLoaded && tmpAmisAppLoaded()
  historyRouter.history.listen(state => {
    amisApp.updateProps({
      location: state.location || state
    });
  });
}
