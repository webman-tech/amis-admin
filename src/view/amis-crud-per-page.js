function initCrudPerPagePersistence(amisLib) {
  if (window.__amisCrudPerPagePersistencePatched) {
    return;
  }

  try {
    const proto = amisLib?.getRendererByName?.('crud')?.Renderer?.prototype;
    if (!proto || proto.__crudPerPagePersistencePatched) {
      window.__amisCrudPerPagePersistencePatched = true;
      return;
    }

    const getStorageKey = () => {
      const route = String(window.location.hash || '').split('?')[0] || window.location.pathname || 'default';
      return `amis_admin:crud_per_page:route:${encodeURIComponent(route)}`;
    };

    const getSavedPerPage = () => {
      try {
        const value = Number.parseInt(window.localStorage?.getItem(getStorageKey()) || '', 10);
        return value > 0 ? value : null;
      } catch (error) {
        return null;
      }
    };

    const hasPerPageInLocation = (field) => {
      const sources = [window.location.search || '', String(window.location.hash || '').split('?')[1] || ''];
      return sources.some((query) => {
        try {
          return new URLSearchParams(query).has(field);
        } catch (error) {
          return false;
        }
      });
    };

    const persistPerPage = (value, defaultValue) => {
      if (!(value > 0)) {
        return;
      }

      try {
        const storageKey = getStorageKey();
        if (defaultValue > 0 && value === defaultValue) {
          window.localStorage?.removeItem(storageKey);
          return;
        }

        window.localStorage?.setItem(storageKey, String(value));
      } catch (error) {}
    };

    const originalDidMount = typeof proto.componentDidMount === 'function' ? proto.componentDidMount : null;
    const originalHandleChangePage = typeof proto.handleChangePage === 'function' ? proto.handleChangePage : null;

    proto.componentDidMount = function (...args) {
      try {
        originalDidMount?.apply(this, args);

        const field = this.props?.perPageField || 'perPage';
        const currentPerPage = Number(this.props?.store?.query?.[field] || this.props?.store?.perPage || this.props?.perPage || 0);
        this.__crudPerPageDefault = Number(this.props?.store?.perPage || this.props?.perPage || 0);

        const savedPerPage = getSavedPerPage();
        if (savedPerPage && !hasPerPageInLocation(field) && savedPerPage !== currentPerPage) {
          this.handleChangePage?.(1, savedPerPage);
        }
      } catch (error) {}
    };

    proto.handleChangePage = async function (page, perPage, dir) {
      if (!originalHandleChangePage) {
        return;
      }

      const result = await originalHandleChangePage.call(this, page, perPage, dir);

      // 仅在 amis 原始分页成功后再同步存储；持久化失败时只降级，不影响列表渲染。
      persistPerPage(Number(perPage || this.props?.store?.perPage || 0), Number(this.__crudPerPageDefault || 0));

      return result;
    };

    proto.__crudPerPagePersistencePatched = true;
    window.__amisCrudPerPagePersistencePatched = true;
  } catch (error) {
    window.__amisCrudPerPagePersistencePatched = true;
  }
}
