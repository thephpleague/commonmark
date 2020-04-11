module Jekyll
  module VersionFilter
    def get_version_type(version)
      releases = @context.registers[:site].data['project']['releases']

      releases.each do |type, versions|
        return type if versions.has_key?(version)
      end

      return nil
    end

    def get_documentation_link(v)
        releases = @context.registers[:site].data['project']['releases']

        releases.each do |type, versions|
          versions.each do |version, info|
            return info['documentation_link'] if version == v
          end
        end

        return nil
    end
  end
end

Liquid::Template.register_filter(Jekyll::VersionFilter)
