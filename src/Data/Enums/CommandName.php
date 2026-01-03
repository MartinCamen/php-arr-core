<?php

namespace MartinCamen\ArrCore\Data\Enums;

enum CommandName: string
{
    case RssSync = 'RssSync';
    case RenameFiles = 'RenameFiles';
    case Backup = 'Backup';
    case ManualImport = 'ManualImport';
    case InteractiveImport = 'InteractiveImport';
    case RefreshMonitoredDownloads = 'RefreshMonitoredDownloads';

    case RefreshSeries = 'RefreshSeries';
    case RescanSeries = 'RescanSeries';
    case RenameSeries = 'RenameSeries';
    case EpisodeSearch = 'EpisodeSearch';
    case SeasonSearch = 'SeasonSearch';
    case SeriesSearch = 'SeriesSearch';
    case MissingEpisodeSearch = 'MissingEpisodeSearch';
    case CutoffUnmetEpisodeSearch = 'CutoffUnmetEpisodeSearch';
    case RefreshMovie = 'RefreshMovie';
    case RescanMovie = 'RescanMovie';
    case RenameMovie = 'RenameMovie';
    case MoviesSearch = 'MoviesSearch';
    case DownloadedMoviesScan = 'DownloadedMoviesScan';
    case MissingMoviesSearch = 'MissingMoviesSearch';
    case CutoffUnmetMoviesSearch = 'CutoffUnmetMoviesSearch';
}
