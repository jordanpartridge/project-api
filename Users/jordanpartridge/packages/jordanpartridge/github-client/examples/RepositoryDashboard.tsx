import React, { useState, useEffect } from 'react';
import { 
  LineChart, 
  Line, 
  XAxis, 
  YAxis, 
  CartesianGrid, 
  Tooltip, 
  Legend, 
  PieChart, 
  Pie, 
  Cell, 
  BarChart, 
  Bar 
} from 'recharts';

// Simulated data structure from our GraphQL query
interface RepositoryInsights {
  name: string;
  description: string;
  url: string;
  createdAt: string;
  starCount: number;
  forkCount: number;
  languages: Array<{
    name: string;
    color: string;
    size: number;
  }>;
  contributors: Array<{
    name: string;
    login: string;
    totalCommits: number;
  }>;
  pullRequests: Array<{
    state: 'OPEN' | 'CLOSED';
    createdAt: string;
    closedAt: string | null;
  }>;
}

// Mock data for demonstration (would come from GraphQL in real scenario)
const mockRepositoryInsights: RepositoryInsights = {
  name: 'github-client',
  description: 'Advanced GitHub API client for Laravel',
  url: 'https://github.com/jordanpartridge/github-client',
  createdAt: '2023-01-15T00:00:00Z',
  starCount: 127,
  forkCount: 24,
  languages: [
    { name: 'TypeScript', color: '#2b7489', size: 68.5 },
    { name: 'PHP', color: '#4F5D95', size: 22.3 },
    { name: 'JavaScript', color: '#f1e05a', size: 9.2 }
  ],
  contributors: [
    { name: 'Jordan Partridge', login: 'jordanpartridge', totalCommits: 342 },
    { name: 'Jane Doe', login: 'janedoe', totalCommits: 127 },
    { name: 'John Smith', login: 'johnsmith', totalCommits: 64 }
  ],
  pullRequests: [
    { state: 'OPEN', createdAt: '2024-02-15T10:00:00Z', closedAt: null },
    { state: 'CLOSED', createdAt: '2024-01-10T14:30:00Z', closedAt: '2024-01-15T16:45:00Z' },
    { state: 'CLOSED', createdAt: '2024-03-01T09:15:00Z', closedAt: '2024-03-05T11:20:00Z' }
  ]
};

const RepositoryDashboard: React.FC = () => {
  const [insights, setInsights] = useState<RepositoryInsights>(mockRepositoryInsights);

  // Language Composition Pie Chart
  const LanguageCompositionChart = () => (
    <div className="bg-white p-4 rounded-lg shadow-md">
      <h3 className="text-lg font-semibold mb-4">Technology Stack</h3>
      <PieChart width={400} height={300}>
        <Pie
          data={insights.languages}
          cx="50%"
          cy="50%"
          labelLine={false}
          outerRadius={80}
          fill="#8884d8"
          dataKey="size"
          label={({ name, percent }) => `${name} ${(percent * 100).toFixed(1)}%`}
        >
          {insights.languages.map((entry, index) => (
            <Cell key={`cell-${index}`} fill={entry.color} />
          ))}
        </Pie>
        <Tooltip />
      </PieChart>
    </div>
  );

  // Contributor Contributions Bar Chart
  const ContributorContributionsChart = () => (
    <div className="bg-white p-4 rounded-lg shadow-md">
      <h3 className="text-lg font-semibold mb-4">Top Contributors</h3>
      <BarChart width={400} height={300} data={insights.contributors}>
        <CartesianGrid strokeDasharray="3 3" />
        <XAxis dataKey="name" />
        <YAxis />
        <Tooltip />
        <Bar dataKey="totalCommits" fill="#8884d8" />
      </BarChart>
    </div>
  );

  // Project Health Timeline
  const ProjectHealthTimeline = () => {
    // Transform pull request data for timeline
    const pullRequestTimeline = insights.pullRequests.map(pr => ({
      date: new Date(pr.createdAt).toLocaleDateString(),
      state: pr.state,
      duration: pr.closedAt 
        ? (new Date(pr.closedAt).getTime() - new Date(pr.createdAt).getTime()) / (1000 * 60 * 60) 
        : 0
    }));

    return (
      <div className="bg-white p-4 rounded-lg shadow-md">
        <h3 className="text-lg font-semibold mb-4">Pull Request Lifecycle</h3>
        <LineChart width={400} height={300} data={pullRequestTimeline}>
          <CartesianGrid strokeDasharray="3 3" />
          <XAxis dataKey="date" />
          <YAxis label={{ value: 'Duration (Hours)', angle: -90, position: 'insideLeft' }} />
          <Tooltip />
          <Line 
            type="monotone" 
            dataKey="duration" 
            stroke="#8884d8" 
            activeDot={{ r: 8 }} 
          />
        </LineChart>
      </div>
    );
  };

  return (
    <div className="p-6 bg-gray-100 min-h-screen">
      <div className="max-w-6xl mx-auto">
        <h1 className="text-3xl font-bold mb-6">{insights.name} Repository Insights</h1>
        
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {/* Repository Overview */}
          <div className="bg-white p-4 rounded-lg shadow-md col-span-full">
            <h2 className="text-xl font-semibold">Repository Overview</h2>
            <div className="grid grid-cols-3 gap-4 mt-4">
              <div>
                <p className="text-gray-600">Stars</p>
                <p className="text-2xl font-bold">{insights.starCount}</p>
              </div>
              <div>
                <p className="text-gray-600">Forks</p>
                <p className="text-2xl font-bold">{insights.forkCount}</p>
              </div>
              <div>
                <p className="text-gray-600">Created</p>
                <p className="text-sm">{new Date(insights.createdAt).toLocaleDateString()}</p>
              </div>
            </div>
          </div>

          {/* Visualization Panels */}
          <LanguageCompositionChart />
          <ContributorContributionsChart />
          <ProjectHealthTimeline />
        </div>
      </div>
    </div>
  );
};

export default RepositoryDashboard;
