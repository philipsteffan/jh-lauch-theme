document.addEventListener('DOMContentLoaded', function () {
  console.log('Hi and welcome to Jugend hackt. I will be your guide!');

  document.querySelector('body').classList.add('js');

  let toc = document.querySelector('.c-toc');
  if (toc) {
    new Toc().init(toc);
  }

  new Sticky().init('.js-sticky', '.js-sticky-container')

  let slider = document.querySelectorAll('.js-slider');
  if (slider.length) {
    new Slider().init(slider);
  }

  let accordion = document.querySelectorAll('[data-accordion]');
  if (accordion.length) {
    accordion.forEach(function(item) {
      new Accordion().init(item);
    })
  }

  let summary = document.querySelectorAll('summary');
  if (summary.length) {
    summary.forEach(function(item) {
      new Summary().init(item);
    })
  }

  new IsoManagement().init('.js-isotope',
                         '.js-isotope > li',
                         '.c-filter select',
                         '.js-filter-parent');
});

function IsoManagement() {
  this.init = function (isoParent, isoChildren, selects, cats) {
    this.selects = document.querySelectorAll(selects);
    this.cats = document.querySelectorAll(cats);
    var elem = document.querySelector(isoParent);

    if (!elem) return false;
    this.filterValues = this.buildFilterValuesFromUrl();
    if (elem && this.selects && this.cats) {
      this.iso = new Isotope( elem, {
        itemSelector: isoChildren,
        layoutMode: 'fitRows'
      });

      this.selects.forEach((v, i) => {
        v.addEventListener('change', this.selectEventWrapper.bind(this));
      });

      this.cats.forEach((v, i) => {
        v.addEventListener('click', this.topicEventWrapper.bind(this));
      });

      // fix layout issues by running this a bit later
      window.setTimeout(() => { this.iso.arrange({filter: Object.values(this.filterValues).join("")}); }, 1000);
      window.setTimeout(() => { this.iso.arrange({filter: Object.values(this.filterValues).join("")}); }, 5000);
    }
  };

  this.buildFilterValuesFromUrl = function() {
    const obj = {};
    if (location.search.length) {
      const query = location.search.slice(1, location.search.length);
      const params = query.split('&');
      params.forEach(item => {
        const key = item.split('=')[0];
        const value = item.split('=')[1];
        obj[key] = '.' + value;
        document.querySelector(`[value="${value}"]`).selected = 'selected'
      })

    }
    return obj;
  };

  this.addToFilterValuesAndFilter = function (key, value) {
    if (value !== '' && value !== undefined) {
      this.filterValues[key] = '.'+ value;
    } else {
      this.filterValues[key] = undefined;
    }
    let filterString = Object.values(this.filterValues).join("");
    const url = Object.keys(this.filterValues).map(item => {
      const value = this.filterValues[item];
      return value ? `${item}=${value.slice(1, value.length)}&` : '';
    });
    history.pushState({}, 'Projekte', `?${url.join('').slice(0, -1)}`);
    this.iso.arrange({filter: filterString});
  };

  this.selectEventWrapper = function (e) {
    this.addToFilterValuesAndFilter(e.target.id, e.target.value);
  };

  this.topicEventWrapper = function (e) {
    e.preventDefault();
    let topicFilter = document.getElementById('filter-topics');
    let value;

    if (e.target.dataset.filter) {
      value = e.target.dataset.filter;
    } else if (e.target.parentElement.dataset.filter) {
      value = e.target.parentElement.dataset.filter;
    } else if (e.target.parentElement.parentElement.dataset.filter) {
      value = e.target.parentElement.parentElement.dataset.filter;
    }

    if (value) {
      value = 'topics-' + value;
      topicFilter.value = value;
      this.addToFilterValuesAndFilter('filter-topics', value);
      //window.scrollTo(0, 300);
    }
  };
}

function Sticky() {
  this.init = function (elemSelector, parentSelector) {
    let elem = document.querySelector(elemSelector);
    let parent = document.querySelector(parentSelector);
    if (elem && parent) {
      this.sticky = elem;
      this.parent = parent;

      window.addEventListener('resize', this.startResizeSpy.bind(this));
      if (window.innerWidth >= 560) {
        document.addEventListener('scroll', this.scrollHelper);
      }
    }
  };

  this.startScrollSpy = function (ev) {
    let pC = this.parent.getBoundingClientRect();
    let sC = this.sticky.getBoundingClientRect();
    if (pC.top < 0) {
      let w = sC.width;
      this.sticky.style.position = 'fixed';
      this.sticky.style.top = '0';
      this.sticky.style.width = w + 'px';

      let endingSpace =  pC.top + pC.height - sC.height;
      if (endingSpace < 0) {
        this.sticky.style.top =  endingSpace +'px';
      }
    } else {
      this.sticky.style.position = 'static';
      this.sticky.style.width = '100%';
    }
  };
  this.startResizeSpy = function (ev) {
    if (window.innerWidth >= 560) {
      document.addEventListener('scroll', this.scrollHelper);
    } else {
      document.removeEventListener('scroll', this.scrollHelper);
    }
  };
  this.scrollHelper = this.startScrollSpy.bind(this);
}

function Slider() {
  this.init = function (sliders) {
    sliders.forEach(item => {
      let options = {};

      if (item.dataset.sliderPreset === "auto") {
        options = {
          container: item,
          controls: false,
          autoplay: true,
          autoplayButtonOutput: false,
          items: 2,
          gutter: 20,
          slideBy: 2,
          nav: false,
          autoplayTimeout: 3000,
          responsive: {
            640: {
              items: 2
            },
            700: {
              gutter: 30
            },
            900: {
              items: 5,
              gutter: 90,
            }
          }
        }
      } else if (item.dataset.sliderPreset === "price") {
        options = {
          container: item,
          fixedWidth: 250,
          autoplay: true,
          autoplayButtonOutput: false,
          slideBy: 3,
          nav: false
        }
      } else if (item.dataset.sliderPreset === "projects") {
        options = {
          container: item,
          autoWidth: true,
          gutter: 90,
          nav: false,
          controlsContainer: '.tns-controls',
        }
      } else {
        options = {
          container: item,
          slideBy: 'page',
          fixedWidth: 350,
          nav: false,
          controlsContainer: '.tns-controls',
        }
      }
      tns(options);
    });
  };
}

function Toc() {
  this.init = function (obj) {
    this.el = obj;
    this.nav = this.el.querySelector('.c-toc-nav');

    if (this.el) {
      this.nav.querySelectorAll('a')
        .forEach(x => x.addEventListener('click', this.toggleEvent.bind(this)));
    }
    if (!this.el.querySelector('.is-active')) {
      let firstElementId = this.nav.querySelector('li:first-of-type a')
          .attributes['href']['nodeValue'];
      this.activateSingle(firstElementId);
    }
  };

  this.toggleEvent = function (ev) {
    ev.preventDefault();
    this.deactivateAll();
    let activeId;
    if (ev.target.attributes.href) {
      activeId = ev.target.attributes['href']['nodeValue'];
    } else {
      activeId = ev.target.parentNode.attributes['href']['nodeValue'];
    }
    this.activateSingle(activeId);
  };

  this.deactivateAll = function () {
    [].concat(...this.el.querySelectorAll('.c-toc-nav a'))
      .concat(...this.el.querySelectorAll('.c-toc-content section'))
      .forEach(x => x.classList.remove('is-active'));
  };

  this.activateSingle = function (id) {
    let navItem = this.el.querySelector(`[href="${id}"]`);
    let contentItem = this.el.querySelector(id);
    if (this.el.dataset.setUrl) {
      const params = new URLSearchParams(location.search);
      params.set('toc', id.substring(1));
      history.replaceState({}, '', '?' + params);
    }

    [navItem, contentItem]
      .forEach(x => x.classList.add('is-active'));
  };
}

function Summary() {
  this.init = function (obj) {
    obj.addEventListener('click', (e) => {
      const params = new URLSearchParams(location.search);
      params.set('faq', e.target.id);
      history.replaceState({}, '', '?' + params);
    })
  };
}

function Accordion() {
  this.init = function ($el) {
    this.$el = $el;
    this.$title = this.$el.querySelector('[data-title]');
    this.$content = this.$el.querySelector('[data-content]');
    this.isOpen = false;
    this.height = 0;

    this.events();
    if (!this.$el.dataset.isOpen) {
      this.close();
    }
  };

  this.events = function() {
    this.$title.addEventListener('click', this.handleClick.bind(this));
    this.$content.addEventListener('transitionend', this.handleTransition.bind(this));
  };

  this.handleClick = function() {
    this.height = this.$content.scrollHeight;

    if (this.isOpen) {
      this.close();
    } else {
      this.open();
    }
  };

  this.close = function() {
    this.isOpen = false;
    this.$el.classList.remove('is-active');
    this.$content.style.maxHeight = `${this.height}px`;

    setTimeout(() => {
      this.$content.style.maxHeight = `${0}px`;
    }, 50);
  };

  this.open = function() {
    this.isOpen = true;
    this.$el.classList.add('is-active');
    this.$el.classList.remove('is-hidden');
    this.$content.style.maxHeight = `${0}px`;

    setTimeout(() => {
      this.$content.style.maxHeight = `${this.height}px`;
    }, 50);
  };

  this.handleTransition = function() {
    if (!this.isOpen) {
      this.$el.classList.add('is-hidden');
    }

    this.$content.style.maxHeight = '';
  };
}
