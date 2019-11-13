"use strict";

import React from "react";
import ReactDOM from "react-dom";

import InfographicDropdown from "./infographicDropdown.jsx";
import SideBar from "./sidebar.jsx";
import {SearchResult, SearchResultContainer} from "./searchResults.jsx";
import CannedSearchContainer from "./cannedSearches.jsx";
import ViewOpts from "./viewOpts.jsx";
import httpGet from "../common/httpGet.js";
import {addUrlQueryParam, getUrlQueryParams} from "../common/queryParams.js";

const CLIENT_ROOT = "..";

const CID_SUNLIGHT = 680;
const CID_MOISTURE = 683;
const CID_WIDTH = 738;
const CID_HEIGHT = 140;

const CHARS_PLANT_FEATURE = [
  612,  // Flower color
  165,  // Bloom months
  685,  // Wildlife support
  136,  // Lifespan
  100,  // Foliage type
  137   // Plant type
];

const CHARS_GROWTH_MAINTENANCE = [
  679,  // Landscape uses
  767,  // Cultivation prefs
  688,  // Plant behavior
  670,  // Propagation
  684   // Ease of growth
];

const CHARS_BEYOND_GARDEN = [
  19,   // Ecoregion
  163   // Habitat
];

function getTaxaPage(tid) {
  return `${CLIENT_ROOT}/taxa/garden.php?taxon=${tid}`;
}

function getVernacularNameStr(item, searchText) {
  const basename = item.basename;
  const names = item.vernacularNames;

  let cname = basename;

  for (let i in names) {
    let currentName = names[i];
    if (searchText.toLowerCase().includes(currentName) || currentName.toLowerCase().includes(searchText)) {
      cname = currentName;
      break;
    }
  }

  if (cname === basename && names.length > 0) {
    cname = names[0];
  }

  if (cname.includes(basename) && basename !== cname) {
    return `${basename}, ${cname.replace(basename, '')}`
  }

  return cname;
}

function getCharacteristics(cids) {
  cids = cids.join(",");
  return new Promise((resolve, reject) => {
    httpGet(`./rpc/api.php?chars=${cids}`)
      .then((res) => {
        resolve(JSON.parse(res));
      })
      .catch((err) => {
        reject(err);
      });
  });
}

function filterByWidth(item, minMax) {
  // TODO: What to do if missing?
  if (!(CID_WIDTH in item.characteristics)) {
    return true;
  }
  const withinMin = Math.min(...item.characteristics[CID_WIDTH]) >= minMax[0];
  if (minMax[1] === 50) {
    return withinMin;
  }
  return withinMin && Math.max(...item.characteristics[CID_WIDTH]) <= minMax[1];
}

function filterByHeight(item, minMax) {
  // TODO: What to do if missing?
  if (!(CID_HEIGHT in item.characteristics)) {
    return true;
  }
  const withinMin = Math.min(...item.characteristics[CID_HEIGHT]) >= minMax[0];
  if (minMax[1] === 50) {
    return withinMin;
  }
  return withinMin && Math.max(...item.characteristics[CID_HEIGHT]) <= minMax[1];
}

function filterByCid(item, cid, cs) {
  return item.characteristics[cid].includes(cs);
}

function filterByChecklist(item, clid) {
  return clid === -1 || item.checklists.includes(clid);
}

function MainContentContainer(props) {
  return (
    <div className="container mx-auto p-4" style={{ maxWidth: "1400px" }}>
      {props.children}
    </div>
  );
}

class GardenPageApp extends React.Component {
  constructor(props) {
    super(props);
    const queryParams = getUrlQueryParams(window.location.search);
    const plantSize = {};
    plantSize[CID_WIDTH] = { value: [0, 50] };
    plantSize[CID_HEIGHT] = { value: [0, 50] };

    this.state = {
      isLoading: false,
      searchResults: [],
      cannedSearches: [],
      plantFeatures: {},
      growthMaintenance: {},
      beyondGarden: {},
      plantNeeds: {
        CID_SUNLIGHT: {
          charname: "",
          states: {},
          value: (CID_SUNLIGHT in queryParams ? parseInt(queryParams[CID_SUNLIGHT.toString()]) : "")
        },
        CID_MOISTURE: {
          charname: "",
          states: {},
          value: (CID_MOISTURE in queryParams ? parseInt(queryParams[CID_MOISTURE.toString()]) : "")
        }
      },
      plantSize: plantSize,
      checklistId: ("clid" in queryParams ? parseInt(queryParams["clid"]) : -1),
      searchText: ("search" in queryParams ? queryParams["search"] : ViewOpts.DEFAULT_SEARCH_TEXT),
      sortBy: ("sortBy" in queryParams ? queryParams["sortBy"] : "vernacularName"),
      viewType: ("viewType" in queryParams ? queryParams["viewType"] : "grid"),
    };

    // To Refresh sliders
    this.sideBarRef = React.createRef();

    this.onSearchTextChanged = this.onSearchTextChanged.bind(this);
    this.onSearch = this.onSearch.bind(this);
    this.onSearchResults = this.onSearchResults.bind(this);
    this.onSortByChanged = this.onSortByChanged.bind(this);
    this.onViewTypeChanged = this.onViewTypeChanged.bind(this);
    this.onFilterRemoved = this.onFilterRemoved.bind(this);
    this.onCannedFilter = this.onCannedFilter.bind(this);
    this.onPlantFeaturesChanged = this.onPlantFeaturesChanged.bind(this);
    this.onPlantNeedChanged = this.onPlantNeedChanged.bind(this);
    this.onGrowthMaintenanceChanged = this.onGrowthMaintenanceChanged.bind(this);
    this.onBeyondGardenChanged = this.onBeyondGardenChanged.bind(this);
    this.onPlantSizeChanged = this.onPlantSizeChanged.bind(this);
  }

  componentDidMount() {
    // Load canned searches
    httpGet(`${CLIENT_ROOT}/garden/rpc/api.php?canned=true`)
      .then((res) => {
        this.setState({ cannedSearches: JSON.parse(res) });
      });

    // Load search results
    this.onSearch();

    // Load sidebar options
    Promise.all([
      getCharacteristics(CHARS_PLANT_FEATURE),
      getCharacteristics(CHARS_GROWTH_MAINTENANCE),
      getCharacteristics(CHARS_BEYOND_GARDEN),
      getCharacteristics([CID_SUNLIGHT, CID_MOISTURE])
    ]).then((res) => {
        let newFeatures = res[0];
        let newGrowth = res[1];
        let newBeyond = res[2];
        let newPlantNeeds = res[3];

        let newFeatureCids = Object.keys(newFeatures);
        for (let i in newFeatureCids) {
          let cid = newFeatureCids[i];
          newFeatures[cid].values = [];
        }

        let newGrowthCids = Object.keys(newGrowth);
        for (let i in newGrowthCids) {
          let cid = newGrowthCids[i];
          newGrowth[cid].values = [];
        }

        let newBeyondCids = Object.keys(newBeyond);
        for (let i in newBeyondCids) {
          let cid = newBeyondCids[i];
          newBeyond[cid].values = [];
        }

        let newPlantNeedCids = Object.keys(newPlantNeeds);
        for (let i in newPlantNeedCids) {
          let cid = newPlantNeedCids[i];
          newPlantNeeds[cid].value = "";
        }

        this.setState({
          plantFeatures: newFeatures,
          growthMaintenance: newGrowth,
          beyondGarden: newBeyond,
          plantNeeds: newPlantNeeds,
        });
      }
    )
    .catch((err) => {
      console.error(err);
    });
  }

  onFilterRemoved(cid) {

  }

  onSearchTextChanged(event) {
    this.setState({ searchText: event.target.value });
  }

  // On search start
  onSearch() {
    const newQueryStr = addUrlQueryParam("search", this.state.searchText);
    window.history.replaceState(
      { query: newQueryStr },
      '',
      window.location.pathname + newQueryStr
    );

    this.setState({ isLoading: true });
    httpGet(`${CLIENT_ROOT}/garden/rpc/api.php?search=${this.state.searchText}`)
      .then((res) => {
        this.onSearchResults(JSON.parse(res));
      })
      .catch((err) => {
        console.error(err);
      })
      .finally(() => {
        this.setState({ isLoading: false });
      });
  }

  // On search end
  onSearchResults(results) {
    let newResults;

    if (this.state.sortBy === "sciName") {
      newResults = results.sort((a, b) => {
        return a["sciName"] > b["sciName"] ? 1 : -1
      });
    } else {
      newResults = results.sort((a, b) => {
        return (
          getVernacularNameStr(a, this.state.searchText).toLowerCase() >
          getVernacularNameStr(b, this.state.searchText).toLowerCase() ? 1 : -1
        );
      });
    }

    this.setState({ searchResults: newResults });
  }

  onPlantSizeChanged(cid, cs) {
    const newSize = Object.assign({}, this.state.plantSize);
    newSize[cid].value = cs;
    this.setState({ plantSize: newSize });
  }

  onPlantNeedChanged(cid, cs) {
    const newPlantNeeds = Object.assign({}, this.state.plantNeeds);
    newPlantNeeds[cid].value = parseInt(cs);
    this.setState({ plantNeeds: newPlantNeeds });
    let newQueryStr = addUrlQueryParam(cid, cs);
    window.history.replaceState({ query: newQueryStr }, '', window.location.pathname + newQueryStr);
  }

  onPlantFeaturesChanged(cid, cs) {
  }

  onGrowthMaintenanceChanged(cid, cs) {
  }

  onBeyondGardenChanged(cid, cs) {
  }

  onSortByChanged(type) {
    let newResults;
    if (type === "sciName") {
      newResults = this.state.searchResults.sort((a, b) => { return a["sciName"] > b["sciName"] ? 1 : -1 });
    } else {
      newResults = this.state.searchResults.sort((a, b) => {
        return (
          getVernacularNameStr(a, this.state.searchText).toLowerCase() >
          getVernacularNameStr(b, this.state.searchText).toLowerCase() ? 1 : -1
        );
      });
    }

    this.setState({
      sortBy: type,
      searchResults: newResults
    });

    let newType;
    if (type === "sciName") {
      newType = type;
    } else {
      newType = '';
    }
    let newQueryStr = addUrlQueryParam("sortBy", newType);
    window.history.replaceState({query: newQueryStr}, '', window.location.pathname + newQueryStr);
  }

  onViewTypeChanged(type) {
    this.setState({ viewType: type });

    let newType;
    if (type === "list") {
      newType = type;
    } else {
      newType = '';
    }
    let newQueryStr = addUrlQueryParam("viewType", newType);
    window.history.replaceState({ query: newQueryStr }, '', window.location.pathname + newQueryStr);
  }

  onCannedFilter(clid) {
    this.setState({ checklistId: clid });
    if (clid === -1) {
      clid = '';
    }
    let newQueryStr = addUrlQueryParam("clid", clid);
    window.history.replaceState({ query: newQueryStr }, '', window.location.pathname + newQueryStr);
  }

  render() {
    const checkListMap = {};
    for (let i in this.state.cannedSearches) {
      let search = this.state.cannedSearches[i];
      checkListMap[search.clid] = search.name;
    }

    return (
      <div>
        <InfographicDropdown />
        <MainContentContainer>
          <div className="row">
            <div className="col-auto">
              <SideBar
                ref={ this.sideBarRef }
                style={{ background: "#DFEFD3" }}
                isLoading={ this.state.isLoading }
                height={ this.state.plantSize[CID_HEIGHT].value }
                width={ this.state.plantSize[CID_WIDTH].value}
                plantNeeds={ this.state.plantNeeds }
                plantFeatures={ this.state.plantFeatures }
                growthMaintenance={ this.state.growthMaintenance }
                beyondGarden={ this.state.beyondGarden }
                searchText={ this.state.searchText }
                onSearch={ this.onSearch }
                onSearchTextChanged={ this.onSearchTextChanged }
                onPlantNeedChanged={ this.onPlantNeedChanged }
                onPlantSizeChanged={ this.onPlantSizeChanged }
                onPlantFeaturesChanged={ this.onPlantFeaturesChanged }
                onGrowthMaintenanceChanged={ this.onGrowthMaintenanceChanged }
                onBeyondGardenChanged={ this.onBeyondGardenChanged }
              />
            </div>
            <div className="col">
              <div className="row">
                <div className="col">
                  <CannedSearchContainer
                    searches={ this.state.cannedSearches }
                    onFilter={ this.onCannedFilter }
                  />
                </div>
              </div>
              <div className="row">
                <div className="col">
                  <ViewOpts
                    viewType={ this.state.viewType }
                    sortBy={ this.state.sortBy }
                    onSortByClicked={ this.onSortByChanged }
                    onViewTypeClicked={ this.onViewTypeChanged }
                    onFilterClicked={ this.onFilterRemoved }
                    checklistNames={ checkListMap }
                    filters={ [] }
                  />
                  <SearchResultContainer viewType={ this.state.viewType }>
                    {
                      this.state.searchResults.map((result) =>  {
                        let filterChecklist = filterByChecklist(result, this.state.checklistId);
                        let filterWidth = filterByWidth(result, this.state.plantSize[CID_WIDTH].value);
                        let filterHeight = filterByHeight(result, this.state.plantSize[CID_HEIGHT].value);
                        let filterSunlight = !(CID_SUNLIGHT in this.state.plantNeeds) || this.state.plantNeeds[CID_SUNLIGHT].value === '' || filterByCid(result, CID_SUNLIGHT, this.state.plantNeeds[CID_SUNLIGHT].value);
                        let filterMoisture = !(CID_MOISTURE in this.state.plantNeeds) || this.state.plantNeeds[CID_MOISTURE].value === '' || filterByCid(result, CID_MOISTURE, this.state.plantNeeds[CID_MOISTURE].value);
                        // let filterFeatures = filterByPlantAttribs(result, "features", this.state.filters.plantFeatures);
                        // let filterGrowthMaint = filterByPlantAttribs(result, "growth_maintenance", this.state.filters.growthMaintenance);
                        // let filterBeyondGarden = filterByPlantAttribs(result, "beyond_garden", this.state.filters.beyondGarden);
                        let showResult = (
                          filterChecklist &&
                          filterWidth &&
                          filterHeight &&
                          filterSunlight &&
                          filterMoisture
                          // filterFeatures &&
                          // filterBeyondGarden &&
                          // filterGrowthMaint
                        );
                        return (
                          <SearchResult
                            key={ result.tid }
                            viewType={ this.state.viewType }
                            display={ showResult }
                            href={ getTaxaPage(result.tid) }
                            thumbnailUrl={ result.thumbnailUrl }
                            commonName={ getVernacularNameStr(result, this.state.searchText) }
                            sciName={ result.sciName ? result.sciName : '' }
                          />
                        )
                      })
                    }
                  </SearchResultContainer>
                </div>
              </div>
            </div>
          </div>
        </MainContentContainer>
      </div>
    );
  }
}

const domContainer = document.getElementById("react-garden");
ReactDOM.render(<GardenPageApp />, domContainer);
