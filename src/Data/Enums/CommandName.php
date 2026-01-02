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
    case EpisodeSearch = 'EpisodeSearch';
    case SeasonSearch = 'SeasonSearch';
    case SeriesSearch = 'SeriesSearch';
    case MissingEpisodeSearch = 'MissingEpisodeSearch';
    case RenameSeries = 'RenameSeries';
    case RefreshMovie = 'RefreshMovie';
    case RescanMovie = 'RescanMovie';
    case MoviesSearch = 'MoviesSearch';
    case DownloadedMoviesScan = 'DownloadedMoviesScan';
    case RenameMovie = 'RenameMovie';
    case MissingMoviesSearch = 'MissingMoviesSearch';
    case CutoffUnmetMoviesSearch = 'CutoffUnmetMoviesSearch';
}
